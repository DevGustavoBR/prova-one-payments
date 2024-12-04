<?php 

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class GerarPix 
{

  public function gerarPix(array $detalhes){
      
      if(empty($detalhes['valor']) || empty($detalhes['descricao_pix']) || empty($detalhes['beneficiario_pix']) || empty($detalhes['chave_pix'])){
        throw new \InvalidArgumentException('Detalhes insuficientes para gerar o Pix.');
      }

      $pixString = sprintf(
   "00020126330014BR.GOV.BCB.PIX0114%s520400005303986540%.2f5802BR5917%s6009SAO PAULO62070503***6304",
    $detalhes['chave_pix'],
            $detalhes['valor'],
            $detalhes['descricao_pix'],
            $detalhes['beneficiario_pix']
      );


      $writer = new PngWriter();

      // Gerar Qrcode
      $qrCode = new QrCode(
          data: $pixString,
          encoding: new Encoding('UTF-8'),
          errorCorrectionLevel: ErrorCorrectionLevel::Low,
          size: 300,
          margin: 10,
          roundBlockSizeMode: RoundBlockSizeMode::Margin,
          foregroundColor: new Color(0, 0, 0),
          backgroundColor: new Color(255, 255, 255)
      );


        $result = $writer->write($qrCode);
        

        $qrCodeBase64 = base64_encode($result->getString());


      return [
        'pix_payload' => $pixString,
        'qr_code' => 'data:image/png;base64,' . $qrCodeBase64,
      ];
  }

}