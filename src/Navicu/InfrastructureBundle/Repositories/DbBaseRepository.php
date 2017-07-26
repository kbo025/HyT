<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @author Mary sanchezs <msmarycarmen@gmail.com>
 * @version 28/05/2016
 */
class DbBaseRepository extends EntityRepository
{
    private $_select;

    private $_from;

    private $_where;

    private $_itemsPerPage;

    private $_currentPage;

    private $_totalItems;

    private $_orderBy;

    private $_orderType;

    private $_tsRank;


    public function findOneByArray($array)
    {
        return $this->findOneBy($array);
    }

    public function save($obj)
    {
        if (is_array($obj))
            foreach ($obj as $one)
                $this->getEntityManager()->persist($one);
        else
            $this->getEntityManager()->persist($obj);
        $this->getEntityManager()->flush();

        return true;
    }

    public function findById($id)
    {
        return $this->find($id);
    }

    public function delete($obj)
    {
        if (is_array($obj))
            foreach ($obj as $one)
                $this->getEntityManager()->remove($one);
        else
            $this->getEntityManager()->remove($obj);
        $this->getEntityManager()->flush();
        return true;
    }

    public function persistObject($obj)
    {
        $this->getEntityManager()->persist($obj);
    }

    public function removeObject($obj)
    {
        $this->getEntityManager()->remove($obj);
    }

    public function flushObject()
    {
        $this->getEntityManager()->flush();
    }

    /**
    * Incluye el select a la sentenci SQL a ejecutar
    *
    * @param    $qb     QueryBuilder
    * @param    $currentPage pagina que se desea retornar
    * @param    $pageSize cantidad de items que deseo retornar
    * @param    $arrayResult indica si debe entregar el resultado como un conjunto de arrays o como un conjunto de objetos
    *
    * @return EntityRepository
    */
    protected function getPaginatedData($qb, $currentPage = 1, $pageSize = 10, $arrayResult = true)
    {

        $cqb = clone $qb;

        $paginator = new Paginator($qb,false);
        $paginator->setUseOutputWalkers(false);
        $paginator
             ->getQuery()
             ->setFirstResult($pageSize * ($currentPage - 1))
             ->setMaxResults($pageSize);

        $totalItems = $cqb
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $pages = ceil( $totalItems / $pageSize );
        $next = ($currentPage == $pages) ? null : $currentPage+1;
        $previous = ($currentPage == 1) ? null : $currentPage-1;


        return [
            'data' => $arrayResult ?
                 $paginator->getQuery()->getArrayResult() :
                 $paginator->getQuery()->getResult(),
            'pagination' => [
                'items' => $totalItems,
                'current' => $currentPage,
                'pages' => $pages,
                'next' => $next,
                'previous' => $previous,
            ]
        ];
    }

    /**
    * Incluye el select a la sentenci SQL a ejecutar
    *
    * @param    $select string
    *
    * @return EntityRepository
    */
    protected function select($select)
    {
        $this->_select = $select;

        return $this;
    }

    /**
    * Incluye el from a la sentenci SQL a ejecutar
    *
    * @param    $from   string      hace referencia a una vista o tabla de la BD
    *
    * @return EntityRepository
    */
    protected function from($from)
    {
        $this->_from = $from;

        return $this;
    }

    /**
     * Incluye el where a la sentenci SQL a ejecutar
     *
     * @param    $where   string
     *
     * @param $additionalCriteria string
     * @return EntityRepository
     */
    protected function where($where, $additionalCriteria = null)
    {
        $this->_where = $where;

        if ( !is_null($additionalCriteria) ) {
            if (!is_null($where))
                $this->_where = $where . " AND " . $additionalCriteria;
            else
                $this->_where = $additionalCriteria;
        }

        return $this;
    }

    /**
    * esta funcion incluye los valores para la paginacion en el SQL a ejecutar
    *
    * @param    $currentPage   integer  pagina que se desea retornar
    * @param    $itemsPerPage   integer  numero de items por pagina
    *
    * @return EntityRepository
    */
    protected function paginate($currentPage,$itemsPerPage)
    {
        $this->_itemsPerPage = $itemsPerPage;
        $this->_currentPage = $currentPage;

        return $this;
    }

    /**
    * esta funcion incluye los valores para el ordenamiento en el SQL
    *
    * @param    $by   string  campo por el que se desea ordenar
    * @param    $type   string '' | 'asc' | 'desc' indica el tipo de ordenamiento
    * @param    $tsRank     string  por este campo se establece el ordenamiento por rank cuando se busca por vector
    *
    * @return EntityRepository
    */
    protected function order($by = null , $type = '', $tsRank = null)
    {
        if(is_null($by)) {
            $date_field_keys = ['date_check_in', 'date_check_out'];
            $foundDateField = in_array($by, $date_field_keys);
            if ($foundDateField) {
                $this->_orderBy = "to_date(" . $by . ",'DD-MM-YYYY') ";
            } else {
                $this->_orderBy = $by;
            }
            $this->_orderType = $type;
        }
        $this->_tsRank = $tsRank;

        return $this;
    }

    /**
    * Devuelve un array con resultados de una cosulta nativa mas su información de paginacion
    *
    * @return Array
    */
    protected function getResults()
    {
        $sql = "SELECT $this->_select FROM $this->_from";

        if(isset($this->_where))
            $sql = $sql." WHERE $this->_where ";

        if(isset($this->_tsRank))
            $sql = $sql." ORDER BY $this->_tsRank desc";

        if(isset($this->_orderBy))
            if(isset($this->_tsRank))
                $sql = $sql.", $this->_orderBy $this->_orderType";
            else
                $sql = $sql." ORDER BY $this->_orderBy $this->_orderType";

        if(isset($this->_itemsPerPage) && isset($this->_currentPage)) {
            $limit = $this->_itemsPerPage;
            $offset = $this->_itemsPerPage * ($this->_currentPage - 1);
            $sql = $sql." LIMIT $this->_itemsPerPage OFFSET $offset";
        }

        $this->_totalItems = $this->getTotalItems();
        $pages = ceil( $this->_totalItems / $this->_itemsPerPage );
        $next = ($this->_currentPage == $pages) ? null : $this->_currentPage+1;
        $previous = ($this->_currentPage == 1) ? null : $this->_currentPage-1;

        return [
            'data' => $this
                ->getEntityManager()
                ->getConnection()
                ->executeQuery($sql)
                ->fetchAll(),
            'pagination' => [
                'items' => $this->_totalItems,
                'current' => $this->_currentPage,
                'pages' => $pages,
                'next' => $next,
                'previous' => $previous,
            ]
        ];
    }

    /**
    * esta funcion devuelve el total de items encontrados para una consulta nativa construida
    *
    * @return integer
    */
    protected function getTotalItems()
    {
        $sql = "SELECT count(id) FROM $this->_from";

        if(isset($this->_where))
            $sql = $sql." WHERE $this->_where ";

        $this->_totalItems =  (integer)$this
                ->getEntityManager()
                ->getConnection()
                ->executeQuery($sql)
                ->fetch()['count'];

        return $this->_totalItems;

    }

    /**
     * Funcion encargada de separar el string ingresado en lo que va a la clausula "where" y al "tsQuery"
     *
     * @param $word array, cadena de palabras a separar y normalizar (Ej: "marÍa check_in:2014-12-14")
     *
     * @param null $arrayOfMatch, arreglo de palabras que coincidiran con los nombre de tipo date en la BD
     * @return null|string where => [check_in:2014-01-01:2014-03-01], tsQuery => [ {maria}, {sunsol}, {2016-02} ]
     * @version 17/01/2017
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function separateByType($word, $arrayOfMatch = null)
    {
        if (is_null($word) || (empty($word)))
            return ["tsQuery" => null, "where" => null];

        $toWhereClause = [];
        $toTsQueryClause = [];
        $lengthMatch = count($arrayOfMatch);

        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-',
            '/[’‘‹›‚]/u'    =>   ' ',
            '/[“”«»„]/u'    =>   ' ',
            '/ /'           =>   ' ',
        );

        // Sustituir el array sin acentos
        $lowerCaseWord = strtolower($word);
        $wordsReplaced = preg_replace(array_keys($utf8), array_values($utf8), $lowerCaseWord);
        // Pasando string a array.
        $words = explode(" ", $wordsReplaced);

        // Eliminando palabras con longitud menor a 2
        $arrayOfWords =  array_filter(
            array_map(function ($w) {
                if (strlen($w) > 2)
                    return $w;
                else
                    return null;
            },
                $words)
        );

        // Patrones necesarios para separar las busquedas ingresadas
        $pattern_text_date = "\d{4}-\d{2}$";
        $pattern_text = "[a-z]\d{0,4}$";
        $pattern_where_date = "\d{4}-\d{2}-\d{2}:\d{4}-\d{2}-\d{2}$";

        // Solo texto
        $foundOnlyText = preg_grep("/$pattern_text/", $arrayOfWords);
        if (!empty( $foundOnlyText)) {
            array_push($toTsQueryClause, $foundOnlyText);
        }

        // Solo las fechas
        $foundOnlyDate = preg_grep("/$pattern_text_date/", $arrayOfWords);
        if (!empty( $foundOnlyDate)) {
            array_push($toTsQueryClause, $foundOnlyDate);
        }

        // Busquedas por rango de fecha
        for ($ii = 0; $ii < $lengthMatch; $ii++) {
            $foundWhereDate = preg_grep("/$arrayOfMatch[$ii]:$pattern_where_date/", $arrayOfWords);
            if (!empty( $foundWhereDate) )
                array_push($toWhereClause, $foundWhereDate);
        }
        return ["tsQuery" => $toTsQueryClause, "where" => $toWhereClause];
    }

    /**
     * Funcion encargada de agregar el sufijo POF para armar un string
     *
     * @param $arrayOfWords array, arreglo de palabras a concatenar con el sufijo
     * @param $suffix string, sufijo que se le agrega a cada token (Ej: ":*")
     * @param string $separator, separador del array (Ej: & |)
     * @return string
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 17/01/2017
     */
    private function addingSuffix($arrayOfWords, $suffix = null, $separator = null)
    {
        if (is_null($arrayOfWords) || (empty($arrayOfWords)))
            return null;

        // Transformamos el string separado por espacios en un array de palabras
        $text = explode(" ", $arrayOfWords);

        // Se unen los elementos mediante el sufijo previsto
        if (is_null($suffix))
            $response = implode(" ".$separator, $text);
        else
            $response = implode($suffix.$separator, $text);

        $response = $response . $suffix;
        return $response;
    }

    /**
     * Funcion encargada de construir el TsQuery necesario para utlizar los vectores
     *
     * @param $words string, conjunto de palabras a buscar
     * @param $searchVector string, nombre del vector el cual contiene las claves por las que buscar
     * @version 18/01/2017
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @return null|string
     */
    protected function getTsQuery(&$words, $searchVector)
    {
        // Sufijo utilizado para la busqueda por ts_vector
        $suffix = ":*";
        $separator = "&";

        $words = $this->normalizedWordToTsQuery($words);
        $words = $this->addingSuffix($words, $suffix, $separator);

        return $words ? "$searchVector @@ to_tsquery('spanish','$words')" : null;
    }

    /**
     * Funcion (depediente del getTsQuery) encargada de construir el TsRank necesario
     * para utilizar los vectores ordenados por prioridad
     *
     * @param $words string, palabra modificada previamente por el metodo getTsQuery
     * @param $searchVector string, nombre del vector el cual contiene las claves por las que buscar
     * @return null|string
     * @version 18/01/2017
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    protected function getTsRank($words, $searchVector)
    {
        $tsQuery = "to_tsquery('spanish','$words')";
        return $words ? "ts_rank($searchVector, $tsQuery)" : null;
    }

    /**
     * Funcion encargada de modificar la palabra ingresada y devolverla normalizada
     *
     * @param $words string, conjunto de palabras a buscar
     * @param $suffix string, sufijo a emplear en cada palabra separada
     * @param $separator string, caracter sobrante al agregarle el sufijo a las palabras
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 18/01/2017
     */
    protected function normalizedWhereClause(&$words, $suffix = null, $separator = null) {
        $words = $this->normalizedWordToTsQuery($words);
        $words = $this->addingSuffix($words, $suffix, $separator);
    }

    /**
     * Funcion encargada de normalizar los arreglos que son utilizados para la busqueda
     * por el vector TsQuery
     *
     * @param $words
     * @return array|string
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 10-02-2017
     */
    private function normalizedWordToTsQuery($words)
    {
        $arrayOfNewWords = [];

        // Si es una busqueda sencilla le quitamos de igual forma los caracteres especiales
        if (strcmp(gettype($words),"string") == 0)
            return preg_replace('([^A-Za-z0-9[:space:]])', "", $words);

        if (is_null($words))
            return null;

        // Si la combinacion de busqueda fue de palabra y fecha simple se concatenan
        // los arreglos internos una vez normalizados
        foreach ($words as $word) {
            // Limpiar el string de simbolos.
            $aux = preg_replace('([^A-Za-z0-9[:space:]])', "", $word);

            // Concatenando el sub-arreglo de palabras
            $aux = implode(" ",$aux);
            array_push($arrayOfNewWords, $aux);
        }

        // Concatenando el arreglo de palabras resultantes
        $arrayOfNewWords = implode(" ", $arrayOfNewWords);

        return $arrayOfNewWords;
    }

    /**
     * Funcion encargada de normalizar las palabras que iran en la seccion del "where"
     * cuando es una busqueda especializada por fecha
     *
     * @param $words
     * @param null $prefix prefijo que se requeriera agregar si no es una vista
     * @return array|string
     * @author Isabel nieto <isabelcnd@gmail.com>
     * @version 2017-02-10
     */
    public function normalizedWordToAdditionalCriteria($words, $prefix = null)
    {
        if (is_null($words))
            return null;
        $arrayOfNewWords = [];
        /**
         *   [
                {
                    1: "check_in:1999-12-15:1999-12-22"
                },
                {
                    4: "check_out:1999-09-25:1999-09-31"
                }
            ],
         */
        // el array no inicia necesariamente en 0 por lo cual hay que tomar aquella
        // posicion que exista y meterla en un unico array de N posiciones
        foreach ($words as $newWord) {
            foreach ($newWord as $key => $value) {
                // Se separa el arreglo
                $date_separated = explode(":", $value);

                // Lo unimos mediante >= AND <=
                $aux = $prefix.$date_separated[0] . " >= '" . $date_separated[1] . "' AND " .
                    $prefix.$date_separated[0] . " <= '" . $date_separated[2] . "'";
                array_push($arrayOfNewWords, $aux);
            }
        }
        // Pegamos los arreglos mediante AND
        $arrayOfNewWords = implode(" AND ", $arrayOfNewWords);

        return $arrayOfNewWords ? $arrayOfNewWords : null;
    }

    public function criteriaByField($field, $criteria)
    {
        $whereClause = '';
        if (is_array($criteria) and count($criteria) > 0){
            $whereClause = ' ' . $field . ' IN (';
            foreach ($criteria as $item) {
                $whereClause = $whereClause . " '" . $item . "'";
                if (next($criteria)==true) $whereClause .= ",";
            }
            $whereClause .= ")";
        } elseif (!is_null($criteria) and !is_array($criteria)) {
            $whereClause = ' ' . $field . '= ' . $criteria . "'";
        }
        return $whereClause;
    }
}
