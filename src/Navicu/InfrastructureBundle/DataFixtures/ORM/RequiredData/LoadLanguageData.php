<?php
namespace Navicu\InfrastructureBundle\DataFixtures\ORM\RequiredData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\Core\Domain\Model\Entity\Language;

/**
 * Clase LoadPropertyServiceData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema, para los tipos de comidas que ofrecen los restaurantes
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadLangugeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */

    public function load(ObjectManager $manager)
    {
        $languages = array(
            array('hin','hindú'),
            array('nld','holandés'),
            array('lat','latín'),
            array('ell','griego'),
            array('ara','árabe'),
            array('pol','polaco'),
            array('fra','francés'),
            array('jpn','japonés'),
            array('rus','ruso'),
            array('zho','chino'),
            array('deu','alemán'),
            array('ita','italiano'),
            array('por','portugués'),
            array('spa','español'),
            array('eng','inglés'),
            /*array('afr','afrikaans'),
            array('amh','amárico'),
            array('arg','aragonés'),
            array('asm','assamés'),
            array('ast','asturiano'),
            array('aym','aymara'),
            array('aze','azerí'),
            array('bak','baskir'),
            array('bam','bambara'),
            array('bel','bielorruso'),
            array('ben','bengalí'),
            array('bis','Bislama'),
            array('bos','bosnio'),
            array('bre','bretón'),
            array('bug','buginés'),
            array('bul','búlgaro'),
            array('cat','catalán'),
            array('ceb','cebuano'),
            array('ces','checo'),
            array('cha','chamorro'),
            array('chv','chuvasio'),
            array('cor','córnico'),
            array('cos','corso'),
            array('cre','cree'),
            array('csb','casubio'),
            array('cym','galés'),
            array('dan','danés'),
            array('div','dhivehi'),
            array('dzo','dzongkha'),
            array('epo','esperanto'),
            array('est','estonio'),
            array('eus','vasco'),
            array('fao','feroés'),
            array('fas','farsi'),
            array('fij','fijiano fidji'),
            array('fil','filipino'),
            array('fin','finés'),
            array('fry','frisón'),
            array('ful','fula'),
            array('fur','friulano'),
            array('gla','gaélico escocés'),
            array('gle','irlandés'),
            array('glg','gallego'),
            array('glv','manés'),
            array('grn','guaraní'),
            array('gug','guaraní'),
            array('hat','criollo haitiano'),
            array('hau','Hausa'),
            array('haw','Hawaiano'),
            array('heb','hebreo'),
            array('hif','hindi de Fiyi'),
            array('hrv','croata'),
            array('hun','húngaro'),
            array('hye','armenio'),
            array('ido','ido'),
            array('ile','Interlingue'),
            array('ilo','ilocano'),
            array('ina','Interlingua'),
            array('ind','indonesio'),
            array('isl','islandés'),
            array('jav','javanés'),
            array('jbo','lojban'),
            array('kal','kalaallisut'),
            array('kat','georgiano'),
            array('kaz','kazajo'),
            array('khm','jemer'),
            array('kin','ruandés'),
            array('kir','kirguís'),
            array('kom','komi'),
            array('kon','kikongo'),
            array('kor','coreano'),
            array('kur','kurdo'),
            array('lad','judeoespañol'),
            array('lao','laosiano'),
            array('lav','letón'),
            array('lim','limburgués'),
            array('lin','lingala'),
            array('lit','lituano'),
            array('ltz','luxemburgués'),
            array('mah','marshalés'),
            array('mar','márata'),
            array('mkd','macedonio'),
            array('mlg','malgache'),
            array('mlt','maltés'),
            array('mol','moldavo'),
            array('mon','mongol'),
            array('mri','maorí'),
            array('msa','malayo'),
            array('mya','birmano'),
            array('nan','chino min nan'),
            array('nap','napolitano'),
            array('nau','nauruano'),
            array('nav','navajo'),
            array('nds','bajo alemán'),
            array('nep','nepalí'),
            array('niu','niuano'),
            array('nno','nynorsk'),
            array('nob','bokmål'),
            array('nor','noruego'),
            array('nya','chichewa'),
            array('oci','occitano'),
            array('oss','osetio'),
            array('pam','pampango'),
            array('pap','papiamento'),
            array('pau','palauano'),
            array('pdc','alemán de Pensilvania'),
            array('pus','pashtu'),
            array('que','quechua'),
            array('rar','rarotongano'),
            array('roh','retorrománico'),
            array('ron','rumano'),
            array('run','rundí'),
            array('rup','arrumano'),
            array('sag','sango'),
            array('scn','siciliano'),
            array('sco','escocés'),
            array('slk','eslovaco'),
            array('slv','esloveno'),
            array('sme','sami septentrional'),
            array('smo','samoano'),
            array('snd','Shindi'),
            array('som','somalí'),
            array('sot','seshoto'),
            array('sqi','albanés'),
            array('srd','sardo'),
            array('srp','serbio'),
            array('ssw','swazi'),
            array('sun','sudanés'),
            array('swa','suajili'),
            array('swb','comorense'),
            array('swe','sueco'),
            array('tah','tahitiano'),
            array('tam','tamil'),
            array('tat','tártaro'),
            array('tel','telugú'),
            array('tet','tetun'),
            array('tgk','tayiko'),
            array('tgl','tagalo'),
            array('tha','tailandés'),
            array('tir','tigriña'),
            array('ton','tongano'),
            array('tpi','tok pisin'),
            array('tsn','tswana'),
            array('tuk','turcomano'),
            array('tum','tumbuka'),
            array('tur','turco'),
            array('tvl','tuvaluano'),
            array('twi','twi'),
            array('ukr','ucraniano'),
            array('urd','urdú'),
            array('uzb','uzbeko'),
            array('vie','vietnamita'),
            array('vol','volapük'),
            array('vor','voro'),
            array('war','samareño'),
            array('wln','valón'),
            array('wol','wolof'),
            array('xho','xhosa'),
            array('yor','yoruba'),
            array('yue','chino cantonés'),
            array('zha','chuang'),
            array('zul','zulú'),*/
        );

        foreach ($languages as $lan) {
            $lan1 = new Language();
            $lan1->setId($lan[0]);
            $lan1->setNative($lan[1]);
            $manager->persist($lan1);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}