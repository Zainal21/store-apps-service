<?php 
namespace App\Helpers;
use Illuminate\Support\Str;
use App\Models\SettingApplication;
use Illuminate\Support\Facades\Auth;


class Helper
{
    protected static $response = [
        'meta' => [
            'code' => 200,
            'success' => true,
            'status' => 'success',
            'message' => null
        ],
        'result' => null
    ];

    public static function success($data = null, $message = null, $code = 200)
    {
        self::$response['meta']['message'] = $message;
        self::$response['meta']['code'] = $code;
        self::$response['result'] = $data;
        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function error($data = null, $message = null, $code = 400)
    {
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['success'] = false;
        self::$response['meta']['message'] = $message;
        self::$response['result'] = $data;
        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function price_to_number($value)
	{
		if (!$value) return 0;
		$value = preg_replace('/[^0-9,]/', '', $value);
		$value = str_replace(',', '.', $value);
		$value = floatval($value);
		return $value;
	}

	public static function number_to_price($value)
	{
		if (!$value) return 0;
		$value = number_format($value, 2, ',', '.');
		return  $value;
	}

    public static function point_to_comma($value)
	{
		$result = str_replace('.', ',', $value);
		return $result;
	}

	public static function comma_to_point($value)
	{
		$result = str_replace(',', '.', $value);
		return $result;
	}

    public static function remove_file($file)
	{
        if (file_exists($file)) {
            unlink($file);
        }
	}

    public static function generate_filename($file,$title = 'title-example',$prefix = 'file-')
	{
        $filename = $prefix . time() . '-' . Str::limit(Str::slug($title), 50, '') . '-' . strtotime('now') . '.' . $file->getClientOriginalExtension();
        return $filename;
    }

    public static function SettingApplication()
    {
        return SettingApplication::where('id','<>', '0')->first();
    }

    public function bni_ganerate_signature($payload, $cliendId, $secretKey)
    {
        $payload = array_merge([
            'clientId' => 'IDBNI'.$cliendId
        ],$payload);
        // Create token header as a JSON string
        $stringHeader = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        // Create token payload as a JSON string
        $stringPayload = json_encode($payload);
        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($stringHeader));
        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($stringPayload));
        // Create Signature Hash
        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . '.' . $base64UrlPayload,
            $secretKey,
            true
        );
        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        // Create JWT
        $jwtSignature = $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;

        $payload = array_merge($payload, ['signature' => $jwtSignature]);

        return json_encode($payload,true);
    }

  
}