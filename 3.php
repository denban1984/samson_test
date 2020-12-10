<?php
declare(strict_types=1);
namespace Test3;

use Exception;

class newBase
{
    static private int $count = 0;
    static private array $arSetName = [];
    /**
     * @param int $name
     */
    function __construct(int $name = 0)
    {
        if (empty($name)) {
            while (array_search(self::$count, self::$arSetName) != false) {
                ++self::$count;
            }
            $name = self::$count;
        }
        $this->name = $name;
        self::$arSetName[] = $this->name;
    }
    protected int $name; /** private $name; */
    /**
     * @return string
     */
    public function getName(): string
    {
        return '*' . $this->name  . '*';
    }
    protected $value;
    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    /**
     * @return int
     */
    public function getSize() :int
    {
       /** $size = strlen(serialize($this->value));
        return strlen($size) + $size;  */
     return strlen(serialize($this->value));
    }
    public function __sleep() :array   /** не был указан возвращаемый тип*/
    {
        return ['value'];
    }
    /**
     * @return string
     */
    public function getSave(): string
    {
        $value = serialize($this->value); /** $value = serialize($value); */
        return $this->name . ':' . strlen($value) . ':' . $value; /**  return $this->name . ':' . sizeof($value) . ':' . $value; */
    }

    /**
     * @param string $value
     * @return newBase
     */
    static public function load(string $value): newBase
    {
        $arValue = explode(':', $value);
        $tmp = new newBase((int)$arValue[0]);
        $tmp->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
            + strlen($arValue[1]) + 1), $arValue));
        return $tmp;
        /**return (new newBase($arValue[0]))
            ->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
                + strlen($arValue[1]) + 1), $arValue[1]));*/
    }
}
class newView extends newBase
{
    private string $type = '' ;
    private int $size = 0;
    private string $property = '';
    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        parent::setValue($value);
        $this->setType();
        $this->setSize();
    }
    public function setProperty($value): newView /** отсутвовал возвращаемый тип*/
    {
        $this->property = $value;
        return $this;
    }
    private function setType()
    {
        $this->type = gettype($this->value);
    }
    private function setSize()
    {
        if (is_subclass_of($this->value, "Test3\\newView")) {   /** был не экранирован слеш "Test3\newView" */
            $this->size = parent::getSize() + 1 + strlen($this->property);
        } elseif ($this->type == 'test') {
            $this->size = parent::getSize();
        } else {
            $this->size = strlen($this->value);
        }
    }
    /**
     * @return array
     */
    public function __sleep() :array
    {
        return ['property'];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getName(): string
    {
        if (empty($this->name)) {
            throw new Exception('The object doesn\'t have name');
        }
        return '"' . $this->name  . '": ';
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return ' type ' . $this->type  . ';';
    }
    /**
     * @return int
     */
    public function getSize(): int
    {
       /** return ' size ' . $this->size . ';';*/
        return $this->size;
    }
    public function getInfo()
    {
        try {
            echo $this->getName()
                . $this->getType()
                . $this->getSize()
                . "\r\n";
        } catch (Exception $exc) {
            echo 'Error: ' . $exc->getMessage();
        }
    }
    /**
     * @return string
     */
    public function getSave(): string
    {
        /** Не совсем понимаю смысл $this->value = $this->value->getSave();
         * У строки вызывается метод?
         *
         * if ($this->type == 'test') {
            $this->value = $this->value->getSave();
        }**/
        return parent::getSave() . serialize($this->property);
    }

    /**
     * @param string $value
     * @return newBase
     */
    static public function load(string $value): newBase
    {
        $arValue = explode(':', $value);
        $tmp = new newView((int)$arValue[0]);
        $tmp->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
            + strlen($arValue[1]) + 1), $arValue));
        $tmp->setProperty(unserialize(substr($value, strlen($arValue[0]) + 1
            + strlen($arValue[1]) + 1 + $arValue[1])));
        return $tmp;
        /**return (new newBase($arValue[0]))
            ->setValue(unserialize(substr($value, strlen($arValue[0]) + 1
                + strlen($arValue[1]) + 1), $arValue[1]))
            ->setProperty(unserialize(substr($value, strlen($arValue[0]) + 1
                + strlen($arValue[1]) + 1 + $arValue[1])))
            ;*/
    }
}
function gettype($value): string
{
    if (is_object($value)) {
        $type = get_class($value);
        do {
            if (strpos($type, "Test3\\newBase") !== false) {  /** был не экранирован слеш "Test3\newBase" */
                return 'test';
            }
        } while ($type = get_parent_class($type));
    }
   return $value; /** если !is_object($value) то все зацикливалось на return gettype($value);*/
}


$obj = new newBase((int)'12345');  /** $obj = new newBase('12345'); */
$obj->setValue('text');

$obj2 = new newView((int)'9876'); /** $obj2 = new \Test3\newView('O9876'); */
$obj2->setValue($obj);
$obj2->setProperty('field');
$obj2->getInfo();

$save = $obj2->getSave();

$obj3 = newView::load($save);

var_dump($obj2->getSave() == $obj3->getSave());

