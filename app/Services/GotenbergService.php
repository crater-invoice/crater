<?php

namespace Crater\Services;

use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Illuminate\Http\Response;

class GotenbergService
{
    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    public function __construct($stream)
    {
        $this->response = $stream;
    }

    public static function fromView(string $viewname): GotenbergService
    {
        $request = Gotenberg::chromium('http://pdf:3000')
            ->margins(0, 0, 0, 0)
            ->paperSize(8.27, 11.7)
            ->html(
                Stream::string(
                    'document.html',
                    view($viewname)->render(),
                )
            );
        $result = Gotenberg::send($request);

        return new GotenbergService($result);
    }

    public function stream(string $filename = 'document.pdf'): Response
    {
        $output = $this->response->getBody();
        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' =>  'inline; filename="' . $filename . '"',
        ]);
    }

    public function output(): string
    {
        return $this->response->getBody()->getContents();
    }
}
