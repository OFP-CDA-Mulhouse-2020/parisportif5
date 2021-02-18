#!/usr/bin/env bash

# Based on code from : https://dev.to/bdelespierre/how-to-setup-git-commit-hooks-for-php-42d1
# Based on code from : https://gist.github.com/Pilipo/e52ff5ac38fba9e1f5ed966816de41e9

# get bash colors and styles here:
# http://misc.flogisoft.com/bash/tip_colors_and_formatting
C_RESET='\e[0m'
C_RED='\e[31m'
C_GREEN='\e[32m'
C_YELLOW='\e[33m'

function __run() #(step, name, cmd)
{
    local color output exitcode

    printf "${C_YELLOW}[%s]${C_RESET} %-20s" "$1" "$2"
    output=$(eval "$3" 2>&1)
    exitcode=$?

    if [[ 0 == $exitcode || 130 == $exitcode ]]; then
        echo -e " - Status : ${C_GREEN} OK !${C_RESET}"
    else
        if [ $1 == "1/5" ]; then
            echo -e " - Status : ${C_YELLOW} SAVE MODIFICATION !${C_RESET}\n\n$output"
        else
            echo -e " - Status : ${C_RED} ERROR DETECTED !${C_RESET}\n\n$output"
        fi
        exit 1
    fi
}

ignore="bin,vendor,automatisation,config,docker,docs,migrations,src/Kernel.php,public,tests/bootstrap.php,templates,translations,var,.github"
phpcbf="vendor/bin/phpcbf --extensions=php --report=code --colors --report-width=80 --standard=PSR12 --encoding=utf-8 --ignore=${ignore} -n -p"
phpcs="vendor/bin/phpcs --extensions=php --report=code --colors --report-width=80 --standard=PSR12 --encoding=utf-8 --ignore=${ignore} -n -p"

__run "1/5" "Code Sniffer : Corrects PSR12 code styling standards" "${phpcbf} src tests"
__run "2/5" "Code Sniffer : Detects violations of PSR12 coding standard" "${phpcs} src tests"
__run "3/5" "PhpStan : Analyse the code" "vendor/bin/phpstan analyse -c phpstan.neon.dist"
__run "4/5" "PhpUnit : Runs unit tests" 'php bin/phpunit -c phpunit.xml --process-isolation --filter "App\\Unit"'
__run "5/5" "PhpUnit : Runs functional tests" 'php bin/phpunit -c phpunit.xml --process-isolation --filter "App\\Functional"'