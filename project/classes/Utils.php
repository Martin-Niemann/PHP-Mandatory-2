<?php

Class Utils
{
    /**
     * Returns the REST API description
     */
    public static function APIDescription(): string 
    {
        return self::addHATEOAS();
    }

    /**
     * Adds HATEOAS links to the data it receives as a parameter
     * 
     * @param array|string $information Entity information to add the HATEOAS links to
     * @param string $entity Name of the entity the HATEOAS links will be added to.
     *                        If nonexistent, only the HATEOAS links will be returned
     * @return string The information to be served by the API including its 
     *          corresponding HATEOAS links encoded as JSON
     */
    public static function addHATEOAS(array|string $information = '', string $entity = ''): string 
    {
        $curDir = self::urlPath();

        if ($entity) {
            $apiInfo[$entity] = $information;
        }
        $apiInfo['_links'] = array(
            array(
                'rel' => ($entity == ENTITY_EMPLOYEES ? 'self' : ENTITY_EMPLOYEES),
                'href' => $curDir . ENTITY_EMPLOYEES . '{?name=&range=&sort=}',
                'type' => 'GET'
            ),
            array(
                'rel' => ($entity == ENTITY_DEPARTMENTS ? 'self' : ENTITY_DEPARTMENTS),
                'href' => $curDir . ENTITY_DEPARTMENTS . '{?sort=}',
                'type' => 'GET'
            )
        );        
        return json_encode($apiInfo);
    }

    /**
     * Returns a format error
     */
    public static function formatError(): string {
        $output['message'] = 'Incorrect format';
        return self::addHATEOAS($output, '_error');
    }    

    /**
     * Returns the API's URL path
     */
    private static function urlPath(): string 
    {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') 
            || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . '/' . basename(__DIR__) . '/';     
    }
}