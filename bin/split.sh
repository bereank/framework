#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="main"

function split()
{
    
    SHA1=`/root/projects/leyscp/framework/bin/splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

function remote()
{
    git remote add $1 $2 || true
}

git pull origin $CURRENT_BRANCH

remote shared git@github.com:leysco100/shared.git
remote gpm git@github.com:leysco100/gpm.git
remote administration git@github.com:leysco100/administration.git
remote business-partner git@github.com:leysco100/business-partner.git
remote inventory git@github.com:leysco100/inventory.git
remote payments git@github.com:leysco100/payments.git

split 'src/Leysco100/Shared' shared
split 'src/Leysco100/Gpm' gpm
split 'src/Leysco100/Administration' administration
split 'src/Leysco100/BusinessPartner' business-partner
split 'src/Leysco100/Inventory' inventory
split 'src/Leysco100/Payments' payments
