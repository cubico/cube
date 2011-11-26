<?php
/*
 * Filename.....: class_mdb.php
 * Class........: mdb
 * Aufgabe......: open *.mdb MSAccess files
 * Erstellt am..: Donnerstag, 17. Juni 2004, 23:32:07
 *       _  __      _ _
 *  ||| | |/ /     (_) |        Wirtschaftsinformatiker IHK
 * \. ./| ' / _ __  _| |_ ___   www.ingoknito.de
 * - ^ -|  < | '_ \| | __/ _ \  
 * / - \| . \| | | | | || (_) | Peter Klauer
 *  ||| |_|\_\_| |_|_|\__\___/  
 * mailto.......: knito@knito.de
 *
 * Changes:
 * 2004-07-21: added function fieldcount()
 */

class mdb
{
  var $RS = 0;
  var $ADODB = 0;
  
  var $strProvider = 'Provider=Microsoft.Jet.OLEDB.4.0';
  var $strMode     = 'Mode=ReadWrite';
  var $strPSI      = 'Persist Security Info=False';
  var $strDataSource  = '';
  var $strConn     = '';
  var $strRealPath = '';
  
  function mdb( $dsn='Please enter DataSource!' )
  {
    $this->strRealPath = realpath( $dsn );
    if( strlen( $this->strRealPath ) > 0 )
    {
      $this->strDataSource = 'Data Source='.$this->strRealPath;
    }
    else
    {
      echo "<br>mdb::mdb() File not found $dsn<br>";
    }
  } // eof constructor mdb()
  
  
  function open( )
  {
    if( strlen( $this->strRealPath ) > 0 )
    {
  
      $this->strConn = 
        $this->strProvider.';'.
        $this->strDataSource.';'.
        $this->strMode.';'.
        $this->strPSI;
        
      $this->ADODB = new COM( 'ADODB.Connection' );
      
      if( $this->ADODB )
      {
        $this->ADODB->open( $this->strConn );
      }
      else
      {
        echo '<br>mdb::open() ERROR with ADODB.Connection<br>'.$this->strConn;
      }
    }
  } // eof open()
  
  function execute( $strSQL )
  {
    $this->RS = $this->ADODB->execute( $strSQL );
  } // eof execute()
  
  function eof()
  {
    return $this->RS->EOF;
  } // eof eof()
  
  function movenext( )
  {
    $this->RS->MoveNext();
  } // eof movenext()
  
  function movefirst()
  {
    $this->RS->MoveFirst();
  } // eof movefirst()
  
  function close()
  {
    $this->RS->Close();
    $this->RS=null;
  
    $this->ADODB->Close();
    $this->ADODB=null;
  } // eof close()
  
  function fieldvalue( $fieldname )
  {
    return $this->RS->Fields[$fieldname]->value;
  } // eof fieldvalue()
  
  function fieldname( $fieldnumber )
  {
    return $this->RS->Fields[$fieldnumber]->name;
  } // eof fieldname()
  
  function fieldcount( )
  {
    return $this->RS->Fields->Count;
  } // eof fieldcount()  
  
} // eoc mdb
?>