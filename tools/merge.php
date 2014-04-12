<?php

class Merger
{
    private $excludedFiles;

    public function __construct(array $paths, array $exclude, $filename)
    {
        $this->excludedFiles = $exclude;
        $this->excludedFiles[] = $filename;

        $files = $this->findPHPFiles($paths);
        $code = $this->mergePHPFiles($files);
        $code = $this->reduceUse($code);

        $this->saveCodeToFile($filename, $code);
    }

    private function mergePHPFiles(array $files)
    {
        $code = '';
        foreach($files as $file) {
            $code .= PHP_EOL . $this->readCodeFromFile($file);
        }

        return $code;
    }

    private function reduceUse($code)
    {
        if (!preg_match_all('/use\s+(\w+)([\\\\]+)(\w+);/i', $code, $matches)) {
           return $code;
        }

        $uses = array_unique($matches[0]);
            $code = str_replace($uses, '', $code);
        
        return implode(PHP_EOL, $uses) . PHP_EOL . $code;
    }

    private function readCodeFromFile($file)
    {
        $code = file_get_contents($file);
        $code = $this->cleanOpenTag($code);
        $code = $this->prepareNamespaces($code);

        return $code;
    }

    private function cleanOpenTag($code)
    {
        $tag = '<?php';
        $tagPosition = strpos($code, $tag);
        if ($tagPosition === false) {
            $tag = '<?hh';
            $tagPosition = strpos($code, $tag);
        }

        return substr($code, $tagPosition + strlen($tag));
    }

    private function prepareNamespaces($code)
    {
        if (!preg_match('/namespace\s+(\w+);/i', $code, $matches)) {
           return $code;
        }

        $ns = $matches[0];

        return str_replace(
            $ns,
            substr($ns, 0, strlen($ns)-1) . ' {',
            $code
        ) . PHP_EOL . '}'; 
    }

    private function findPHPFiles(array $paths)
    {
        $files = [];
        foreach($paths as $path) {
            $files = array_merge($files, $this->globRecursive($path . '/*.php'));
        }

        return array_diff($files, $this->excludedFiles);
    }

    private function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge(
                $files,
                $this->globRecursive($dir.'/'.basename($pattern), $flags)
            );
        }

        return $files;
    }

    private function saveCodeToFile($filename, $code)
    {
        file_put_contents($filename, '<?hh' . PHP_EOL . $code);
    }
}

(new Merger(
    ['src', 'vendor/mongofill/mongofill/src'], 
    ['vendor/mongofill/mongofill/src/functions.php'],
    'src/ext_mongo.php'
));
