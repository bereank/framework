<?php

namespace Leysco100\Gpm\Mail;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Leysco100\Shared\Models\OUQR;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Leysco100\Gpm\Reports\AlertScanReport;
use Leysco100\Shared\Models\Administration\Models\ALR2;
use Leysco100\Shared\Models\Administration\Models\ALT3;
use Leysco100\Shared\Models\Administration\Models\OALR;

class AlertMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $alert = OALR::with('alert_template.alt5', 'alert_template.alt6.saved_query')->where('id', $this->id)->first();
        $attachments = [];

        if ($alert) {

            foreach ($alert->alert_template->alt6 as $query) {
                $QueryRes = $this->processQuery($query->saved_query->QString);

                if ($QueryRes) {
                    $fileName = $query->saved_query->QName . $query->id . '.xlsx';
                    Excel::store(new AlertScanReport($QueryRes), $fileName);
                    $attachmentPath = Storage::path($fileName);
                    $attachments[] = $attachmentPath;
                }
            }
        } else {
            exit;
        }

        $data = ALR2::where('Code', $this->id)
            ->with(['lines' => function ($query) {
                $query->select('Location', 'Value');
            }])
            ->select('ColName', 'Location')
            ->get();

        $tempBody =  $this->process($alert->alert_template->alt5->tempBody);

        // Log::info(nl2br($tempBody));

        $mail = $this->subject($alert->alert_template->alt5->tempSubject)
            ->markdown('gpm::alertNotification')
            ->with('data', $data)
            ->with('tempBody', nl2br($tempBody))
            ->with('template', $alert->alert_template->alt5);

        foreach ($attachments as $attachment) {
            $mail->attach($attachment);
        }

        return $mail;
    }

    public function processQuery($query)
    {


        $result = DB::connection('tenant')->select($query);

        $headers = [];
        if (is_array($result)) {
            if (!empty($result)) {
                $headers = array_keys((array)$result[0]);
            }

            $formattedResult = [];
            foreach ($result as $row) {
                $formattedResult[] = (array)$row;
            }

            $results = [
                'headers' => $headers,
                'data' => $formattedResult
            ];
        }
    
        return $results;
    }

    public function process($templateStr)
    {

        $matches = $this->getMatchedTemplateVariables($templateStr);

        if ($matches && count($matches) > 0) {
            $templateVariables = $this->getParsedTemplateVariables($matches);
        }
        if (!empty($templateVariables)) {
            $compiled = $this->replaceKeysWithValues($templateVariables, $templateStr);
            return $compiled;
        } else {
            return    $templateStr;
        }
    }

    private function getMatchedTemplateVariables($templateStr)
    {
        $regex = '/\[\w.+?\]/m';

        preg_match_all($regex, $templateStr, $matches, PREG_SET_ORDER);

        return $matches;
    }

    private function getParsedTemplateVariables($matches)
    {
        $templateVariables = [];
        foreach ($matches as $match) {
            if (Str::contains($match[0], "VAR:")) {
                $cleanedStr = str_replace(['[VAR:', ']'], '', $match[0]);
                $mailVariable = ALT3::where('variable_key', $cleanedStr)->first();
                if ($mailVariable) {
                    $templateVariables[$match[0]] = $mailVariable->variable_value;
                }
            } else if (Str::contains($match[0], "TABLE:")) {
                // Log::info('Has a Table');
                $cleanedStr = str_replace(['[TABLE:', ']'], '', $match[0]);
                // Log::info(['Has a Table', $cleanedStr]);
                $mailVariable = OUQR::where('QCode',  $cleanedStr)->first();
                if ($mailVariable->QString) {
                    $data = $this->processQuery($mailVariable->QString);
                    // Create the HTML table markup
                    $table = '<table>';
                    $table .= '<thead><tr>';
                    foreach ($data['headers'] as $header) {
                        $table .= '<th>' . $header . '</th>';
                    }
                    $table .= '</tr></thead>';
                    $table .= '<tbody>';
                    foreach ($data['data'] as $row) {
                        $table .= '<tr>';
                        foreach ($row as $cell) {
                            $table .= '<td>' . $cell . '</td>';
                        }
                        $table .= '</tr>';
                    }
                    $table .= '</tbody>';
                    $table .= '</table>';
                }

                $templateVariables[$match[0]] = $table;
            } else {
                Str::contains($match[0], "[', ']");
                $cleanedStr = str_replace(['[', ']'], '', $match[0]);

                $mailVariable = OUQR::where('QCode',  $cleanedStr)->first();
                if ($mailVariable->QString) {

                    $quryResult = DB::connection('tenant')->select($mailVariable->QString);

                    $formattedResult = [];
                    foreach ($quryResult as $row) {
                        $formattedResult[] = (array)$row;
                    }

                    $value = reset($formattedResult[0]);

                    $templateVariables[$match[0]] = $value;
                }
            }
        }

        return $templateVariables;
    }

    private function replaceKeysWithValues($templateVariables, $templateStr)
    {
        return str_replace(array_keys($templateVariables), array_values($templateVariables), $templateStr);
    }
}
