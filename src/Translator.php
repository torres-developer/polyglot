<?php

namespace TorresDeveloper\Polyglot;

use Psr\Http\Message\UriInterface;
use TorresDeveloper\HTTPMessage\HTTPVerb;

use function TorresDeveloper\Pull\pull;

class Translator
{
    private const LANGUAGES = "/languages";
    private const TRANSLATE = "/translate";

    private UriInterface $ws;
    private ?Lang $native;

    private LangsGraph $langs;

    public function __construct(UriInterface $ws, ?Lang $native = null)
    {
        $this->ws = $ws;
        $this->native = $native;

        $this->getLangs(true);
    }

    public function getLangs(bool $force = false): LangsGraph
    {
        if ($force || !isset($this->langs)) {
            $this->createGraph();
        }

        return $this->langs;
    }

    public function translate(string $text, Lang $to): string
    {
        $this->native = $this->langs->getVertix($this->native);

        $path = $this->native->shortPathTo($to);

        $last = $this->native;

        /** @var Lang $lang */
        foreach ($path as $lang) {
            if ($lang == $this->native) {
                continue;
            }

            $body = $this->post(self::TRANSLATE, [
                "q" => $text,
                "source" => $last->getCode(),
                "target" => $lang->getCode(),
            ]);

            $last = $lang;

            $text = $body["translatedText"];
        }

        return $text;
    }

    public function getNative(): Lang
    {
        return $this->native;
    }

    public function setNative(Lang $lang): void
    {
        $this->native = $lang;
    }

    private function post(string $path, array $formData): array
    {
        return json_decode(pull(
            $this->ws->withPath($path),
            HTTPVerb::POST,
            http_build_query($formData),
            ["Content-Type" => "multipart/form-data"]
        ), true);
    }

    private function createGraph(): void
    {
        $this->langs = new LangsGraph();

        $langs = json_decode(pull($this->ws->withPath(self::LANGUAGES)), true);

        $this->langs->addVertix(...array_map(
            self::parseLang(...),
            $langs
        ));

        foreach ($langs as $entry) {
            $lang = self::parseLang($entry);

            foreach ($entry["targets"] as $target) {
                $this->langs->addEdge($lang, new Lang($target));
            }
        }
    }

    private static function parseLang(array $entry): Lang
    {
        $lang = new Lang($entry["code"], $entry["name"]);

        return $lang;
    }
}
