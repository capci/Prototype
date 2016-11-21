Capci\Prototype
====

PHP7でプロトタイプベースのオブジェクト指向プログラミングを実現するライブラリ。


## 概要

クラスベースのオブジェクト指向言語であるPHPで、JavaScriptのようなプロトタイプベースのオブジェクト指向プログラミングを可能にします。
JavaScriptライクではありますが、プロトタイプチェーンはサポートしておらず、オブジェクトの複製で機能を拡張していきます。

## 使い方

### ライブラリのロード
autoload.phpを読み込みます。

    require './Capci/Prototype/autoload.php';

### 空オブジェクトの作成とプロパティのセット、ゲット

\Capci\Prototype\Objectクラスのインスタンスをnewし、プロパティに値をセットします。

    use \Capci\Prototype\Object;
    $object = new Object;
    $object->prop = 3;
    echo $object->prop; //=> 3

配列のようなアクセスも可能です。

    $object['prop'] = 'foo';
    echo $object['prop']; //=> 'foo'

存在しないプロパティにアクセスした場合、OutOfRangeExceptionがスローされます。

    try {
        $object->notExists;
        //$object['notExists'];
    } catch(\Throwable $ex) {
        var_dump($ex); //=> \OutOfRangeException
    }

### プロパティの存在確認と削除

hasKeyメソッドでプロパティが存在するか確認できます。  
isset関数では、プロパティが存在していても値がnullの場合、falseを返します。

    $object->prop = null;
    var_dump($object->hasKey('prop')); //=> true
    var_dump(isset($object->prop)); //=> false
    var_dump(isset($object['prop'])); //=> false

unset関数でプロパティの削除が可能です。

    unset($object->prop);
    //unset($object['prop']);
    var_dump($object->hasKey('prop')); //=> false

### インスタンスメソッドのセットと実行

プロパティに関数（Closure）をセットすることで、インスタンスメソッドとして実行できます。

    $object->prop = 3;
    $object->add = function(int $num) {
        // $thisには$objectがバインドされます。
        return $this->prop + $num;
    };
    echo $object->add(2); //=> 5

インスタンスメソッドとして実行できない場合、BadMethodCallExceptionがスローされます。

    try {
        $object->prop();
    } catch(\Throwable $ex) {
        var_dump($ex); //=> \BadMethodCallException
    }

### オブジェクトの複製と機能の拡張

clone構文によりオブジェクトを複製できます。複製されたオブジェクトにプロパティを追加することで、機能を拡張できます。

    $object->prop = 3;
    $object->add = function(int $num) {
        return $this->prop + $num;
    };
    
    $newObject = clone $object;
    $newObject->prop = 5;
    $newObject->sub = function(int $num) {
        return $this->prop - $num;
    };
    echo $newObject->add(2); //=> 7

cloneではプロパティがシャローコピーされます。

    $o = new \stdClass;
    $object->prop = $o;
    $newObject = clone $object;
    var_dump($o === $newObject->prop); //=> true

## ライセンス

MIT License http://www.opensource.org/licenses/mit-license.php
