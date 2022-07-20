<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\RutFormat;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;


// use

class RutController extends Controller
{

    const XPATH_RAZON_SOCIAL = '//html/body/div/div[4]';
    const XPATH_RUT_CONTRIBUYENTE = '//html/body/div/div[6]';
    const XPATH_INICIO_ACTIVIDADES = '//html/body/div/span[2]';
    const XPATH_FECHA_INICIO_ACTIVIDADES = '//html/body/div/span[3]';
    const XPATH_AUTORIZADO_MONEDA_EXTRANJERA = '//html/body/div/span[4]';
    const XPATH_EMPRESA_MENOR_TAMANO_PRO_PYME = '//html/body/div/span[5]';
    const XPATH_ACTIVITIES   = '//html/body/div/table[1]/tr';

    public function index($rut)
    {
        if (count(explode('-', $rut)) != 2) return response()->json(["message" => "El código de verificación no coincidía con el RUT. El RUT y el código deben estar delimitados con -"], 402);

        $crawler = new Crawler(self::fetch($rut));
        $razonSocial = ucwords(strtolower(trim($crawler->filterXPath(self::XPATH_RAZON_SOCIAL)->text())));
        $rutContribuyente = ucwords(strtolower(trim($crawler->filterXPath(self::XPATH_RUT_CONTRIBUYENTE)->text())));

        $inicioActividades = explode(':', ucwords($crawler->filterXPath(self::XPATH_INICIO_ACTIVIDADES)->text()));
        count($inicioActividades) == 2 ? $inicioActividades = trim($inicioActividades[1]) : $inicioActividades = trim($inicioActividades[0]);

        $fechaInicioActividades = explode(':', ucwords($crawler->filterXPath(self::XPATH_FECHA_INICIO_ACTIVIDADES)->text()));
        count($fechaInicioActividades) == 2 ? $fechaInicioActividades = trim($fechaInicioActividades[1]) : $fechaInicioActividades = trim($fechaInicioActividades[0]);

        $autorizadoMonedaExtranjera = explode(':', ucwords($crawler->filterXPath(self::XPATH_AUTORIZADO_MONEDA_EXTRANJERA)->text()));
        count($autorizadoMonedaExtranjera) == 2 ? $autorizadoMonedaExtranjera = trim($autorizadoMonedaExtranjera[1]) : $autorizadoMonedaExtranjera = trim($autorizadoMonedaExtranjera[0]);

        $empresaMenorTamanoProPyme = explode(':', ucwords($crawler->filterXPath(self::XPATH_EMPRESA_MENOR_TAMANO_PRO_PYME)->text()));
        count($empresaMenorTamanoProPyme) == 2 ? $empresaMenorTamanoProPyme = trim($empresaMenorTamanoProPyme[1]) : $empresaMenorTamanoProPyme = trim($empresaMenorTamanoProPyme[0]);

        $actividades = [];
        $crawler->filterXPath(self::XPATH_ACTIVITIES)->each(function (Crawler $node, $i) use (&$actividades) {
            if ($i > 0) {
                $actividades[] = [
                    'giro'      => $node->filterXPath('//td[1]/font')->text(),
                    'codigo'    => (int)$node->filterXPath('//td[2]/font')->text(),
                    'categoria' => $node->filterXPath('//td[3]/font')->text(),
                    'afecta'    => $node->filterXPath('//td[4]/font')->text() == 'Si'
                ];
            }
        });

        $documentos = [];
        $crawler->filter('table')->last()->filter('tr')->each(function (Crawler $node, $i) use (&$documentos) {
            if ($i > 0) {
                $documentos[] = [
                    'tipo_documento'      => $node->filterXPath('//td[1]/font')->text(),
                    'ultimo_anho_timbraje'    => (int)$node->filterXPath('//td[2]/font')->text()
                ];
            }
        });

        return [
            'razonSocial' => $razonSocial,
            'rut' => $rutContribuyente,
            'inicio_actividades' => $inicioActividades == 'SI' ? true : false,
            'fecha_inicio_actividades' =>  $fechaInicioActividades,
            'autorizado_moneda_extranjera' => $autorizadoMonedaExtranjera == 'SI' ? true : false,
            'empresa_menor_tamano_pro_pyme' => $empresaMenorTamanoProPyme == 'SI' ? true : false,
            'actividades' => $actividades,
            'documentos_timbrados' =>  $documentos,
            'actuaciones' => [
                'tipo_actuacion' => 'MODIFICACION',
                'fecha_actuacion' => '',
                'url_pdf_detalle' => 'https://www.dsdsd'
            ]
        ];
    }


    public function fetch($rut)
    {

        $rut = new RutFormat(trim($rut));
        $url = "https://zeus.sii.cl/cvc_cgi/stc/getstc";
        $captcha = self::fetchCaptcha();
        $response = Http::asForm()->post($url, [
            'RUT' => $rut->number,
            'DV'  => $rut->code,
            'PRG' => 'STC',
            'OPC' => 'NOR',
            'txt_code' => $captcha['code'],
            'txt_captcha' => $captcha['txtCaptcha']
        ]);
        return $response->getBody()->getContents();
    }

    public function fetchCaptcha()
    {
        $url = "https://zeus.sii.cl/cvc_cgi/stc/CViewCaptcha.cgi";
        $response = Http::asForm()->post($url, [
            'oper' => 0
        ]);
        $response = json_decode($response->body(), true);
        $code = substr(base64_decode($response['txtCaptcha']), 36, 4);
        return [
            "txtCaptcha" => $response['txtCaptcha'],
            "code" => $code
        ];
    }
}
