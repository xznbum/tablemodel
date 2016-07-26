<?php

namespace csv\Controller;
use csv\Model\Model;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    private $model;
    private $jsonResponse;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function get (Request $request)
    {
        $json = $this->model->getRows();
        return new JsonResponse($json, Response::HTTP_OK);
    }

    public function post (Request $request)
    {
        $row = json_decode($request->getContent(), true);
        $offset = $this->model->addRow($row);
        $this->jsonResponse = new JsonResponse([ 'offset' => $offset], Response::HTTP_CREATED);
        return $this->jsonResponse;
    }

    public function getRow (Request $request, $offset)
    {
        $json = $this->model->getRow($offset);
        if (is_array($json)) {
            $this->jsonResponse = new JsonResponse($json, Response::HTTP_OK);
        } else {
            $this->jsonResponse = new JsonResponse(Response::HTTP_NOT_FOUND);
        }
        return $this->jsonResponse;
    }

    public function put(Request $request, $offset)
    {
        $row = json_decode($request->getContent(), true);
        if ($this->model->updateRow($offset, $row) == true) {
            $this->jsonResponse = new JsonResponse(Response::HTTP_OK);
        } else {
            $this->jsonResponse = new JsonResponse(Response::HTTP_NOT_FOUND);
        }
        return $this->jsonResponse;
    }

    public function delete (Request $request, $offset)
    {
        if ($this->model->deleteRow($offset) == true) {
            $this->jsonResponse = new JsonResponse(Response::HTTP_OK);
        } else {
            $this->jsonResponse = new JsonResponse(Response::HTTP_NOT_FOUND);
        }
        return $this->jsonResponse;
    }
}