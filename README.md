# onvif-qr

These are scripts to incorporate into plug-ins to get ONVIF streams easily. It realies heavily on the ponvif library that has been modified to work with Profile T cameras.

camera-entry.php

This script contains an example form to get the authenication information for an ip cctv piece of onvif compliant equipment for downloading. In it's current form it assumes the user is autheniacted over https with digest authentaction. If the user is 'admin' then you can choose to create a file that a user with the name entered in the 'client username' field can later use to get streams from the device. The information is posted to qr-encode.php.

qr-encode.php

This file encodes the data as a flat file or a reocrd in a database for later retrieval by the user. A link to qr-decode.php with the proper tokens is encoded in a QR Code for easy addition to mobile clients such as cell phones or tablets. It is assumed qr-decode is on a server with the same authentication scheme and user name as qr-encode.

qr-decode.php

This file decodes the back end record based on the passed in tokens, connects to the device with the ponvif library and displays a pick list of stream tokens. The pick list could just be re-written as cvs output and parsed by a mobile client. The form is posted to streams.php.

streams.php

Outputs the selected stream.

login-class.php

a simple login class to bypass the use of any backend authentication. Use for debuging and development purposes only.

logout.php

logs the user out.

lib/auth-class.php

simple authentication class. Use for debugging and development purposes only.

lib/class.ponvif.php

A modified version of ponvif from https://github.com/ltoscano/ponvif to use Profile T compliant cameras.

lib/class.qr.php

class to generate and decode authentication records and qr codes.
zlib.php

a few custom library functions.



For questions or any support contact sales@zwebusa.com. This offer implies no warranty of any kind.



