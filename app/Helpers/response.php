<?php

function collectionResponse($message, $data, $page, $per_page, $total_data, $total_page)
{
    return response()->json([
        'status'     => 'success',
        'message'    => $message,
        'data'       => $data,
        'page'       => $page,
        'per_page'   => $per_page,
        'total_data' => $total_data,
        'total_page' => $total_page
    ], 200);
}

function successResponse($message, $data)
{
    return response()->json([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ], 200);
}

function errorResponse($message, $code)
{
    return response()->json([
        'status' => 'error',
        'message' => $message,
    ], $code);
}