Cerb5 Plugins - net.pixelinstrument.sla
=======================================
Copyright (C) 2011 Davide Cassenti
[http://davide.cassenti.it/](http://davide.cassenti.it/)  

What's this?
------------
The plugin allows to manage the SLA (response time) of the tickets, based on the customer's type. The settings allow to decide, for each type of customer, what is the limit in days or business days to reply.

A class `PiSlaUtils` is provided in order to allow external plugins to use these information.

Installation
------------
* Change directory to **/cerb5/storage/plugins/**
* `git clone git://github.com/cerb5-plugins/net.pixelinstrument.sla.git`
* In your helpdesk, enable the plugin from **Setup->Features & Plugins**
* Create a new Picklist Custom Field for the Organization object for the customer type (it can have any name)
* Change plugin settings

Credits
-------
This plugin was developed by [Davide Cassenti](http://davide.cassenti.it/).

License
-------

GPLv3 
[http://www.gnu.org/licenses/gpl-3.0.txt](http://www.gnu.org/licenses/gpl-3.0.txt)  
