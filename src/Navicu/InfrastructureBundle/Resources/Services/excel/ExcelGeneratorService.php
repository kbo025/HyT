<?php

namespace Navicu\InfrastructureBundle\Resources\Services\excel;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * servicio que funcionara como intermediario para crear archivos excel mediante la libreria phpexcel
 *
 * @author Gabriel Camacho <gcamacho@navicu.com>
 * @version 04-11-2016
 */
class ExcelGeneratorService
{

    /**
     * estilos utilizados como cabecera de una tabla
     */
    private $header_styles;

    /**
     * estilos utilizados para la data de una tabla
     */
    private $data_styles;

    /**
     * contenedor de servicios de symfony
     */
    private $container;

    /**
     * constructor
     *
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;

        $this->header_styles = [
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            /*'borders' => [
                'allborders' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                ],
            ],*/
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '391462'],
            ],
        ];

        $this->data_styles = [
            'borders' => [
                'left' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '391462'],
                ],
                'right' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '391462'],
                ],
                'bottom' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => ['rgb' => '391462'],
                ],
            ],
            'fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'EDEDED'],
            ],
            'font' => [
                'color' => ['rgb' => '706F6F'],
            ],
        ];
    }

    /**
     * crear un excel de aavv afiliadas
     *
     * @param $data
     * @return mixed
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     * @version 04-11-2016
     */
    public function getExcelAffiliatesAAVV($data)
    {
        $phpExcelObject = $this->container->get('xls.service_xls2007')->excelObj;
        //$phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject();
        //$writer = $this->container->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

        $phpExcelObject
            ->getProperties()
            ->setCreator("Navicu")
            ->setTitle("Navicu aavv Registradas");

        $i = 2;
        $phpExcelObject->setActiveSheetIndex(0);
        $activeSheet = $phpExcelObject->getActiveSheet();
        $activeSheet->getColumnDimension('A')->setWidth('5');
        $activeSheet->getColumnDimension('B')->setWidth('15');
        $activeSheet->getColumnDimension('C')->setWidth('12');
        $activeSheet->getColumnDimension('D')->setWidth('15');
        $activeSheet->getColumnDimension('E')->setWidth('12');
        $activeSheet->getColumnDimension('F')->setWidth('12');
        $activeSheet->getColumnDimension('G')->setWidth('12');
        $activeSheet->getColumnDimension('H')->setWidth('12');
        $activeSheet
            ->setCellValue('A1', 'NUM')
            ->setCellValue('B1', 'Nombre')
            ->setCellValue('C1', 'Código')
            ->setCellValue('D1', 'Localidad')
            ->setCellValue('E1', 'Fecha de Alta')
            ->setCellValue('F1', 'Facturación Acumulada')
            ->setCellValue('G1', 'Facturación Mensual')
            ->setCellValue('H1', 'Crédito disponible');
        $activeSheet
            ->getStyle('A1:H1')
            ->applyFromArray($this->header_styles);
        foreach ($data as $item) {
            $activeSheet
                ->setCellValue('A'.$i, $i-1)
                ->setCellValue('B'.$i, !empty($item['name']) ? $item['name'] : null)
                ->setCellValue('C'.$i, !empty($item['publicId']) ? $item['publicId'] : null)
                ->setCellValue('D'.$i, !empty($item['city']) ? $item['city'] : null)
                ->setCellValue('E'.$i, !empty($item['joinDate']) ? $item['joinDate'] : null)
                ->setCellValue('F'.$i, !empty($item['invoicesAccumulated']) ? $item['invoicesAccumulated'] : 0)
                ->setCellValue('G'.$i, !empty($item['invoicesMonth']) ? $item['invoicesMonth'] : 0)
                ->setCellValue('H'.$i, !empty($item['availabilityCredit']) ? $item['availabilityCredit'] : 0);

            $activeSheet
                ->getStyle('E'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS);

            $activeSheet
                ->getStyle('F'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

            $activeSheet
                ->getStyle('G'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

            $activeSheet
                ->getStyle('H'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

            $i++;
        }

        $activeSheet
            ->getStyle('A2:A'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('B2:B'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('C2:C'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('D2:D'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('E2:E'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('F2:F'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('G2:G'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('H2:H'.$i)
            ->applyFromArray($this->data_styles);

        $phpExcelObject->getActiveSheet()->setTitle('aavv');

        $response = $this->container->get('xls.service_xls2007')->getResponse();
        /*$response = $this->container->get('phpexcel')->createStreamedResponse($writer);*/
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'NavicuAavv.xlsx'
        );  
        
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * crear un excel de aavv en proceso de registro
     *
     * @param $data
     * @return mixed
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     * @version 04-11-2016
     */
    public function getExcelInProcessAAVV($data)
    {
        //todo lo comentado es porque se cambio a una version mas vieja del bundle por la version de php en produccion
        $phpExcelObject = $this->container->get('xls.service_xls2007')->excelObj;
        //$phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject();
        //$writer = $this->container->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

        $phpExcelObject
            ->getProperties()
            ->setCreator("Navicu")
            ->setTitle("Navicu aavv en proceso de registro");

        $i = 2;
        $phpExcelObject->setActiveSheetIndex(0);
        $activeSheet = $phpExcelObject->getActiveSheet();
        $activeSheet->getColumnDimension('A')->setWidth('5');
        $activeSheet->getColumnDimension('B')->setWidth('15');
        $activeSheet->getColumnDimension('C')->setWidth('12');
        $activeSheet->getColumnDimension('D')->setWidth('15');
        $activeSheet->getColumnDimension('E')->setWidth('12');
        $activeSheet->getColumnDimension('F')->setWidth('7');
        $activeSheet
            ->setCellValue('A1', 'NUM')
            ->setCellValue('B1', 'Nombre')
            ->setCellValue('C1', 'Código')
            ->setCellValue('D1', 'Localidad')
            ->setCellValue('E1', 'Fecha de Registro')
            ->setCellValue('F1', 'Completado');
        $activeSheet
            ->getStyle('A1:F1')
            ->applyFromArray($this->header_styles);

        foreach ($data as $item) {
            $activeSheet
                ->setCellValue('A' . $i, $i - 1)
                ->setCellValue('B' . $i, $item['nameAgency'])
                ->setCellValue('C' . $i, $item['idAgency'])
                ->setCellValue('D' . $i, $item['city'])
                ->setCellValue('E' . $i, $item['beginDate'])
                ->setCellValue('F' . $i, $item['percentComplete']/100);

            $activeSheet
                ->getStyle('E'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS);

            $activeSheet
                ->getStyle('F'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

            $i++;
        }
        $activeSheet
            ->getStyle('A2:A'.$i)
            ->applyFromArray($this->data_styles);
        $activeSheet
            ->getStyle('B2:B'.$i)
            ->applyFromArray($this->data_styles);
        $activeSheet
            ->getStyle('C2:C'.$i)
            ->applyFromArray($this->data_styles);
        $activeSheet
            ->getStyle('D2:D'.$i)
            ->applyFromArray($this->data_styles);
        $activeSheet
            ->getStyle('E2:E'.$i)
            ->applyFromArray($this->data_styles);
        $activeSheet
            ->getStyle('F2:F'.$i)
            ->applyFromArray($this->data_styles);

        $phpExcelObject->getActiveSheet()->setTitle('aavv');

        $response = $this->container->get('xls.service_xls2007')->getResponse();
        /*$response = $this->container->get('phpexcel')->createStreamedResponse($writer);*/
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'NavicuAavv.xlsx'
        );  
        
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * crear un excel de aavv afiliadas
     *
     * @param $data
     * @return mixed
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     * @version 18-04-2017
     */
    public function getExcelReservations($data)
    {
        $phpExcelObject = $this->container->get('xls.service_xls2007')->excelObj;
        //$phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject();
        //$writer = $this->container->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

        $phpExcelObject
            ->getProperties()
            ->setCreator("Navicu")
            ->setTitle("Navicu Reservas");

        $i = 2;
        $phpExcelObject->setActiveSheetIndex(0);
        $activeSheet = $phpExcelObject->getActiveSheet();
        $activeSheet->getColumnDimension('A')->setWidth('5'); //Num
        $activeSheet->getColumnDimension('B')->setWidth('10'); //public_id
        $activeSheet->getColumnDimension('C')->setWidth('20'); //client_name
        $activeSheet->getColumnDimension('D')->setWidth('20'); //name_property
        $activeSheet->getColumnDimension('E')->setWidth('15'); //city
        $activeSheet->getColumnDimension('F')->setWidth('12'); //create_date
        $activeSheet->getColumnDimension('G')->setWidth('12'); //check_in
        $activeSheet->getColumnDimension('H')->setWidth('12'); //check_out
        $activeSheet->getColumnDimension('I')->setWidth('12'); //total_to_pay
        $activeSheet->getColumnDimension('J')->setWidth('12'); //status
        $activeSheet
            ->setCellValue('A1', 'Num')
            ->setCellValue('B1', 'id')
            ->setCellValue('C1', 'Cliente')
            ->setCellValue('D1', 'Establecimiento')
            ->setCellValue('E1', 'Ciudad')
            ->setCellValue('F1', 'Fecha de Reserva')
            ->setCellValue('G1', 'CheckIn')
            ->setCellValue('H1', 'CheckOut')
            ->setCellValue('I1', 'Total')
            ->setCellValue('J1', 'Estado');
        $activeSheet
            ->getStyle('A1:J1')
            ->applyFromArray($this->header_styles);
        foreach ($data as $item) {
            $activeSheet
                ->setCellValue('A'.$i, $i-1)
                ->setCellValue('B'.$i, !empty($item['public_id']) ? $item['public_id'] : null)
                ->setCellValue('C'.$i, !empty($item['client_name']) ? $item['client_name'] : null)
                ->setCellValue('D'.$i, !empty($item['name_property']) ? $item['name_property'] : null)
                ->setCellValue('E'.$i, !empty($item['city']) ? $item['city'] : null)
                ->setCellValue('F'.$i, !empty($item['create_date']) ? $item['create_date'] : null)
                ->setCellValue('G'.$i, !empty($item['check_in']) ? $item['check_in'] : null)
                ->setCellValue('H'.$i, !empty($item['check_out']) ? $item['check_out'] : null)
                ->setCellValue('I'.$i, !empty($item['total_to_pay']) ? $item['total_to_pay'] : null)
                ->setCellValue('J'.$i, !empty($item['status']) ? $item['status'] : null);

            $activeSheet
                ->getStyle('F'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS);

            $activeSheet
                ->getStyle('G'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS);

            $activeSheet
                ->getStyle('H'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS);

            $activeSheet
                ->getStyle('I'.$i)
                ->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

            $i++;
        }

        $activeSheet
            ->getStyle('A2:A'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('B2:B'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('C2:C'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('D2:D'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('E2:E'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('F2:F'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('G2:G'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('H2:H'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('I2:I'.$i)
            ->applyFromArray($this->data_styles);

        $activeSheet
            ->getStyle('J2:J'.$i)
            ->applyFromArray($this->data_styles);

        $phpExcelObject->getActiveSheet()->setTitle('Reservas');

        $response = $this->container->get('xls.service_xls2007')->getResponse();
        /*$response = $this->container->get('phpexcel')->createStreamedResponse($writer);*/
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'NavicuReservas.xlsx'
        );  
        
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}