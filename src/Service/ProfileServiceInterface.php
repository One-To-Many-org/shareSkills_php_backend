<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Request;

interface ProfileServiceInterface
{
    /**
     * @param Request $request
     * @param $id
     * @param array $context //to allow to use other parmeter
     * @return mixed
     */
    public function update(Request $request, $id,$context=[]);

    public function read(Request $request,$id,$context=[]);

    public function all(Request $request,$context=[]);

    public function delete($id,$context=[]);
}
