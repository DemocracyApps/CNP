## Community Narratives Platform

The Community Narratives Platform is a tool to help key community players better play their roles.

### License

The Community Narratives Platform is open-sourced software licensed under the [GNU GPL V3 license](http://www.gnu.org/copyleft/gpl.html)

### Environment Setup

The CNP uses the Laravel framework. The easiest way to set up a development environment is to follow the instructions at https://github.com/DemocracyApps/CNP-Dev-Env.

You will need to add passwords, API keys, etc. in various 
files (app/config/mail.php and app/config/packages/artdarek/oauth-4-laravel/config.php). Make the changes 
and be sure to run the following on each such file

    git update-index --assume-unchanged _filename_
    
Email confirmations and CSV uploads require that the queuing system be up and running. You may need to run 

    sudo service supervisor start
    
to get it started.
