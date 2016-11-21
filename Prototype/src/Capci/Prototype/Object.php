<?php
/**
 * @package Capci\Prototype
 * @version 1.0
 * @author capci https://github.com/capci
 * @link https://github.com/capci/Prototype Capci\Prototype
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

declare (strict_types = 1);

namespace Capci\Prototype;

/**
 * キーとそれに関連付けられた値の集合からなる、連想配列です。
 * 
 * このクラスのオブジェクトは値に関数（Closure）を登録でき、インスタンスメソッドとして実行できます。
 * 
 * clone構文によりオブジェクトの複製が可能です。
 * 複製されたオブジェクトに対し新しいプロパティをセットすることで、オブジェクトの機能を拡張できます。
 */
final class Object implements \ArrayAccess, \Countable, \IteratorAggregate {
    
    private $properties;
    
    /**
     * 新しい空のオブジェクトを作成します。
     */
    public function __construct() {
        $this->properties = [];
    }
    
    /**
     * このオブジェクトに指定したキーのプロパティが存在するか調べます。
     * 
     * isset関数と違い、値がnullでもtrueを返します。
     * 
     * @param string $key キー。
     * @return bool 指定したキーのプロパティが存在する場合true、そうでない場合false。
     */
    public function hasKey(string $key): bool {
        return array_key_exists($key, $this->properties);
    }
    
    /**
     * このオブジェクトのすべてのプロパティのキーを返します。
     * 
     * @return array このオブジェクトのすべてのプロパティのキー。
     */
    public function keys(): array {
        return array_keys($this->properties);
    }

    /**
     * このオブジェクトのすべてのプロパティにアクセスするためのイテレータを返します。
     * 
     * @return \Traversable このオブジェクトのすべてのプロパティにアクセスするためのイテレータ。
     */
    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->properties);
    }

    /**
     * このオブジェクトに指定したキーのプロパティをセットします。
     * 
     * @param string $key キー。
     * @param mixed $value 値。
     */
    public function __set(string $key, $value) {
        $this->properties[$key] = $value;
    }
    
    /**
     * このオブジェクトから指定したキーのプロパティを削除します。
     * 
     * @param string $key キー。
     */
    public function __unset(string $key) {
        unset($this->properties[$key]);
    }
    
    /**
     * このオブジェクトに指定したキーのプロパティが存在し、かつその値がnullでないかを調べます。
     * 
     * hasKeyメソッドと違い、値がnullの場合falseを返します。<br>
     * この動作により、isset関数の引数にこのオブジェクトを渡しても正しく動作します。
     * 
     * @param string $key キー。
     * @return bool 指定したキーのプロパティが存在し、かつその値がnullでない場合true、そうでない場合false。
     */
    public function __isset(string $key): bool {
        return isset($this->properties[$key]);
    }
    
    /**
     * このオブジェクトの指定したキーで、プロパティの値を返します。
     * 
     * @param string $key キー。
     * @return mixed 指定したキーのプロパティの値。
     * @throws \OutOfRangeException 存在しないキーを指定した場合。
     */
    public function __get(string $key) {
        if(!array_key_exists($key, $this->properties)) {
            throw new \OutOfRangeException('Undefined property: ' . $key);
        }
        return $this->properties[$key];
    }

    /**
     * このオブジェクトに指定したキーのプロパティをセットします。
     * 
     * @param string $key キー。
     * @param mixed $value 値。
     */
    public function offsetSet($key, $value) {
        $this->__set($key, $value);
    }

    /**
     * このオブジェクトから指定したキーのプロパティを削除します。
     * 
     * @param string $key キー。
     */
    public function offsetUnset($key) {
        $this->__unset($key);
    }

    /**
     * このオブジェクトに指定したキーのプロパティが存在し、かつその値がnullでないかを調べます。
     * 
     * hasKeyメソッドと違い、値がnullの場合falseを返します。<br>
     * この動作により、isset関数の引数にこのオブジェクトを渡しても正しく動作します。
     * 
     * @param string $key キー。
     * @return bool 指定したキーのプロパティが存在し、かつその値がnullでない場合true、そうでない場合false。
     */
    public function offsetExists($key): bool {
        return $this->__isset($key);
    }

    /**
     * このオブジェクトの指定したキーで、プロパティの値を返します。
     * 
     * @param string $key キー。
     * @return mixed 指定したキーのプロパティの値。
     * @throws \OutOfRangeException 存在しないキーを指定した場合。
     */
    public function offsetGet($key) {
        return $this->__get($key);
    }

    /**
     * このオブジェクトのプロパティの数を返します。
     * 
     * @return int このオブジェクトのプロパティの数。
     */
    public function count(): int {
        return count($this->properties);
    }

    /**
     * このオブジェクトの指定したキーで取得できるプロパティの値を、インスタンスメソッドとして実行します。
     * 
     * @param string $key キー。
     * @param array $arguments メソッドの引数。
     * @return mixed メソッドの戻り値。
     * @throws \BadMethodCallException 存在しないキーを指定した場合、もしくは指定したキーの値が実行可能（Closure）出ない場合。
     * @throws \Throwable 実行したメソッドからスローされた例外。
     */
    public function __call(string $key, array $arguments) {
        try {
            $closure = $this->__get($key);
        } catch (\OutOfRangeException $ex) {
            throw new \BadMethodCallException('Undefined method ' . $key, 0, $ex);
        }
        if(!is_callable($closure)) {
            throw new \BadMethodCallException('Property ' . $key . ' is not callable');
        }
        return call_user_func_array($closure->bindTo($this), $arguments);
    }

    /**
     * このオブジェクトの複製を作成します。
     * 
     * オブジェクトのプロパティは、すべてシャローコピーされます。
     */
    public function __clone() {
    }
}