<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head></head>
    <body>
        <h1>Cloudmailin for cloudControl</h1>
        <div style="width: 600px;">
            <p>
                This mini app on <a href="http://www.cloudcontrol.com" target="_blank">cloudControl</a> 
                shows the usage of the cloudmailin addon.
            </p>
            <p>
                <a href="http://cloudmailin.com" target="_blank">The cloudmailin addon</a> provides that e-mail messages, sent to 
                a specific cloudmailin e-mail address are forwarded to your webapp.
            </p>
            <p>
                The goal in this mini app is to store the incoming messages to a database.
                To run this mini app you need a cloudControl mysqls addon and naturally a cloudControl cloudmailin addon.
            </p>
            <p>
                To run this app you have to
                <ul>
                    <li>
                    configure your miniapp incoming 
                    messages endpoint on cloudmailin.com.
                        <ul>
                            <li>
                            Login on cloudmailin.com with your cloudmailin credentials (type in commandline <code>$ cctrlapp APP_NAME/DEP_NAME addon</code>)
                            </li>
                            <li>
                            On the page to manage the "Email Forwards (SMTP)", choose the proper cloudmailin email address and click on "manage".
                            You can "Edit Target", fill the target field with "http://APP_NAME.cloudcontrolled.com/incomingMail.php"
                            </li>
                        </ul>
                    </li>
                    <li>
                        Configure your database. Connect to your database with your mysqls credentials 
                        (type in commandline <code>$ cctrlapp APP_NAME/DEP_NAME addon</code>) <br/>
                        Create a table in your database:
                        <pre>
CREATE TABLE `mail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `from` varchar(255) DEFAULT '',
    `to` varchar(255) DEFAULT '',
    `subject` varchar(255) DEFAULT '',
    `plain` varchar(2048) DEFAULT '',
    `html` varchar(4096) DEFAULT '',
    `x_remote_ip` varchar(128) DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
                        </pre>
                    </li>
                    <li>
                        Now you can send an e-mail to the cloudmailin email address from your default e-mail client.
                        Cloudmailin forwards the e-mail to your webapp. The incomingMail.php script stores the mail to the database.
                    </li>
                    <li>
                        You can read the mail on a request to "http://APP_NAME.cloudcontrolled.com/readMail.php"
                    </li>
                </ul>
            </p>
        </div>
    </body>
</html>