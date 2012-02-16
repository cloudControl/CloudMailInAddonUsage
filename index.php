<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head></head>
    <body>
        <h1>CloudmailIn for cloudControl</h1>
        <div style="width: 600px;">
            <p>
                This mini app on <a href="http://www.cloudcontrol.com"
target="_blank">cloudControl</a>
                shows the usage of the CloudMailIn addon.
            </p>
            <p>
                <a href="http://cloudmailin.com" target="_blank">The
CloudMailIn addon</a> provides incoming email for your app.
            </p>
            <p>
                The goal in this mini app is to store the incoming
messages to - in this case - a database.
                To run the app you need a cloudControl MySQLs
addon and naturally a cloudControl CloudMailIn addon.
            </p>
            <p>
                To run this app you have to
                <ul>
                    <li>
                    configure the endpoint of incoming emails to CloudMailIn.com.
                        <ul>
                            <li>
                            Login on CloudMailIn.com with your
CloudMailIn credentials (type in commandline <code>$ cctrlapp
APP_NAME/DEP_NAME addon</code>)
                            </li>
                            <li>
                            On CloudMailIn's dashboard at "Email Forwards
(SMTP)", choose the proper CloudMailIn email address and click on
"manage".
                            Click on "Edit Target" and fill the target
field with "http://APP_NAME.cloudcontrolled.com/incomingMail.php"
                            </li>
                        </ul>
                    </li>
                    <li>
                        Configure your database. Connect to your
database with your mysqls credentials
                        (type in commandline <code>$ cctrlapp
APP_NAME/DEP_NAME addon</code>) <br/>
                        Create a table in your database:
                        <pre>
CREATE TABLE `mail` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE
CURRENT_TIMESTAMP,
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
                        Now you can send an e-mail to the CloudMailIn 
email address from your default e-mail client.
                        CloudMailIn forwards the e-mail to your
webapp. The incomingMail.php script stores the mail to the MySQLs database.
                    </li>
                    <li>
                        You can read the mail with a request to
"http://APP_NAME.cloudcontrolled.com/readMail.php"
                    </li>
                </ul>
            </p>
        </div>
    </body>
</html>