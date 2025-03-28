<?php

namespace App\Tools;

use Exception;
use Illuminate\Support\Facades\Storage;
trait OtherTrait
{
    public function GenerateResponse(int $status, $figures = [], $message = '', $isbearer = false): array
    {
        if ($message == '')
            $OkMsg = 'Ok';
        else
            $OkMsg = $message;
        $statuslist = [
            '200' => $OkMsg,
            '500' => 'OOps! server error..try again ' . $message,
            '401' => 'Unauthorized request..Bad user'.$message,
            '400' =>  $message,
            '501' => 'OOps! server error..not implemented try again ' . $message,
            '442' => 'Invalid data : ' . $message,
            // '403' => 'Forbidden.. Access to this page not granted'
            '403' => 'Forbidden.. Access to this page not granted,    ' .$message
            
        ];
        if (array_key_exists($status, $statuslist)) {
            $message = $statuslist[$status];
        }
        if ($isbearer) {
            return $figures;
        } else {
            return [
                'status' => $status,
                'message' => $message
            ];
        }
    }
    
    public function StoreException(Exception $ex)
    {
        $errorstring = "****************************Exception****************************\n";
        $errorstring .= "Date-Time : " . date('Y-m-d H:i:s') . "\n";
        $errorstring .= "Trace :" . $ex->getTraceAsString();
        $errorstring .= "Error : " . $ex->getMessage() . "\n";
        $errorstring .= "***************************End-Exception*************************\n";
        $fileName = 'log/error.txt';
        Storage::disk('public')->append($fileName, $errorstring);
    }
    public function FormatDataToString(array $data = []): string
    {
        $formatedstring = '';
        foreach ($data as $key => $val) {
            $formatedstring .= $key . '|' . trim($val) . ',';
        }
        $formatedstring = substr($formatedstring, 0, -1);
        return $formatedstring;
    }
    public function FormatStringToData(string $data = ''): array
    {
        $data = explode(',', $data);
        $formatedarray = [];
        for ($i = 0; $i < count($data); $i++) {
            $val = explode('|', $data[$i]);
            $formatedarray += [
                $val[0] => $val[1]
            ];
        }
        return $formatedarray;
    }
}