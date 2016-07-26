<?php

namespace csv\Model;

class Model implements TableModel
{
    private $path;
    private $table;

    public function __construct($path)
    {
        $this->path = $path;
        $this->load();
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return array_keys($this->table[0]);
    }

    /**
     * @return void
     */
    public function load()
    {
        $keys = array(); // массив для заголовков
        $this->table = array();
        // функция конвертирует csv в ассоциативный массив
        function csvToArray($file, $delimiter) {
            // открытие файла
            if (($handle = fopen($file, 'r')) !== FALSE) {
                $i = 0;
                //  вставка значений в массив
                while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
                    for ($j = 0; $j < count($lineArray); $j++) {
                        $arr[$i][$j] = $lineArray[$j];
                    }
                    $i++;
                }
                fclose($handle);
            }
            return $arr;
        }

        $data = csvToArray($this->path, ',');
        /*  количество строк уменьшено на один,
            поскольку в первой строке быди заголовки
        */
        $count = count($data) - 1;

        //  добавление заголовков в отдельный массив
        $labels = array_shift($data);
        foreach ($labels as $label) {
            $keys[] = $label;
        }

        //  соединение ключей и значений
        for ($j = 0; $j < $count; $j++) {
            $d = array_combine($keys, $data[$j]);
            $this->table[$j] = $d;
        }
    }

    /**
     * @return void
     */
    public function save()
    {
        $fp = fopen($this->path, 'w');
        fputcsv($fp, $this->getHeaders());
        foreach ($this->table as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }

    /**
     * @param array $row
     * @return int offset
     */
    public function addRow(array $row)
    {
        array_push($this->table, $row);
        $this->save();
        return $this->countRows();
    }

    /**
     * @param int $offset
     * @param array $row
     * @return boolean
     */
    public function updateRow($offset, array $row)
    {
        $isOk = false;
        // array_key_exists — проверяет, присутствует ли в массиве указанный индекс
        if (array_key_exists($offset - 1, $this->table)) {
            $this->table[$offset - 1] = $row;
            $this->save();
            $isOk = true;
        };
        return $isOk;
    }

    /**
     * @param int $offset
     * @return array|null
     */
    public function getRow($offset)
    {
        $result = null;
        if (array_key_exists($offset - 1, $this->table)) {
            $result = $this->table[$offset - 1];
        };
        return $result;
    }

    /**
     * @param $offset integer
     * @return boolean
     */
    public function deleteRow($offset)
    {
        $isOk = false;
        if ($this->countRows() >= $offset) {
            array_splice($this->table, $offset - 1, 1);
            $this->save();
            $isOk = true;
        }
        return $isOk;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->table;
    }

    /**
     * @return int
     */
    public function countRows()
    {
        return count($this->table);
    }
}