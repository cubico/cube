<?php
/**
 * Constants.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.1
 */
define("SMSMASS_CONN_CLOSED", 30);
define("SMSMASS_CONN_OPEN", 31);
define("SMSMASS_CONN_LISTENING", 32);
define("SMSMASS_CONN_CONNECTION_PENDING", 33);
define("SMSMASS_CONN_RESOLVING_HOST", 34);
define("SMSMASS_CONN_HOST_RESOLVED", 35);
define("SMSMASS_CONN_CONNECTING", 36);
define("SMSMASS_CONN_CONNECTED", 37);
define("SMSMASS_CONN_CLOSING", 38);
define("SMSMASS_CONN_ERROR", 39);
define("SMSMASS_OK", 1);
define("SMSMASS_OK_ANY_RECIPIENT_INVALID", 2);
define("SMSMASS_NOOK_GENERIC_ERROR", 0);
define("SMSMASS_NOOK_ALL_RECIPIENT_INVALID", 3);
define("SMSMASS_NOOK_NO_RECIPIENTS", 4);
define("SMSMASS_NOOK_NO_TEXT_OR_TEXT_TOO_LONG", 5);
define("SMSMASS_NOOK_INSUFFICIENT_CREDIT", 6);
define("SMSMASS_NOOK_INVALID_DELIVER_ID", 7);
define("SMSMASS_NOOK_NOT_CONNECTED_TO_HOST", 100);
define("SMSMASS_NOOK_TEXT_NOT_FOUND", 101);
define("SMSMASS_NOOK_RECIPIENTS_NOT_FOUND", 102);
define("SMSMASS_NOOK_DATA_NOT_FOUND", 103);
define("SMSMASS_NOOK_DATA_TOO_LONG", 104);
define("SMSMASS_NOOK_INVALID_NOKIA_OPERATOR_LOGO_DATA", 105);
define("SMSMASS_NOOK_INVALID_NOKIA_OPERATOR_LOGO_SIZE", 106);
define("SMSMASS_NOOK_INVALID_NOKIA_GROUP_LOGO_DATA", 107);
define("SMSMASS_NOOK_INVALID_NOKIA_GROUP_LOGO_SIZE", 108);
define("SMSMASS_NOOK_INVALID_EMS_LOGO_DATA", 109);
define("SMSMASS_NOOK_INVALID_EMS_LOGO_SIZE", 110);
define("SMSMASS_NOOK_ANY_OR_ALL_TEXT_NOT_FOUND", 111);
define("SMSMASS_NOOK_ANY_OR_ALL_DATA_NOT_FOUND", 112);
define("SMSMASS_NOOK_ANY_OR_ALL_DATA_TOO_LONG", 113);
define("SMSMASS_NOOK_SUBJECT_NOT_FOUND", 114);
define("SMSMASS_NOOK_MIMETYPE_OR_DATA_NOT_FOUND", 115);
define("SMSMASS_NOOK_ABNORMAL_ERROR", 116);
define("SMSMASS_NOOK_NUMBER_OF_TEXT_AND_RECIPIENTS_NOT_MATCH", 117);
define("SMSMASS_NOOK_ANY_OR_ALL_RECIPIENTS_NOT_FOUND", 118);
define("SMSMASS_NOOK_NUMBER_OF_DATA_AND_RECIPIENTS_NOT_MATCH", 119);
define("SMSMASS_ERR_ALREADY_LOGGED", 200);
define("SMSMASS_ERR_INVALID_USER_OR_PASSWORD", 201);
define("SMSMASS_ERR_PROTOCOL_ERROR", 202);
define("SMSMASS_ERR_SOCKET_ERROR", 203);
define("SMSMASS_ERR_CANT_SEND_DATA", 204);
define("SMSMASS_ERR_CANT_GET_DATA", 205);
define("SMSMASS_ERR_ANOTHER_CONNECTION_IN_PROGRESS", 206);
define("SMSMASS_ERR_NO_USER_OR_PASSWORD_FOUND", 207);
define("SMSMASS_ERR_NO_HOST_FOUND", 208);
define("SMSMASS_ERR_CANT_CONNECT_TO_HOST", 209);
define("SMSMASS_ERR_ABNORMAL_ERROR", 210);
define("SMSMASS_ERR_PING_TIMEOUT", 211);
define("SMSMASS_DEL_UNKNOWN", 300);
define("SMSMASS_DEL_ACKED", 301);
define("SMSMASS_DEL_BUFFERED", 302);
define("SMSMASS_DEL_DELIVERED", 303);
define("SMSMASS_DEL_FAILED", 304);
?>