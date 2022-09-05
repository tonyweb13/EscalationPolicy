<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Utils\VDate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * When doing a GET request, network.js encodes the JSON into a parameter named "json".  This middle-ware
 * sets the JSON on the request.  Normally the JSON is set from the content during a POST/PUT, etc, but
 * JQuery/XMLHTTPRequest doesn't allow sending content with a GET request, so we encode as a query
 * parameter instead.
 *
 * @package App\Http\Middleware
 */
class SetFieldsFromJSON
{
    public function handle($request, Closure $next)
    {
        if ($request instanceof Request && $request->isMethod('GET')) {
            $json = $request->query->get('json');
            if ($json) {
                $parameter_bag = new ParameterBag((array) json_decode($json, true));
                $this->convertBag($parameter_bag);
                $request->setJson($parameter_bag);
            }
        } elseif ($request->isJson()) {
            $parameter_bag = $request->json();
            $this->convertBag($parameter_bag);
        }
        return $next($request);
    }

    protected function convertBag(ParameterBag $bag): void
    {
        $all = $bag->all();
        $this->convertPart($all);
        $bag->replace($all);
    }

    protected function convertPart(&$part): void
    {
        if (array_key_exists('date3_marker', $part)) {
            $part = VDate::parse($part['date3_text'], VDate::YEAR_TO_DAY_NUMS);
        } else {
            foreach ($part as &$sub_part) {
                if (is_array($sub_part)) {
                    $this->convertPart($sub_part);
                }
            }
        }
    }
}
