# ECMAScript 6 特性介绍

## 介绍
ECMAScript 6, 也称为ECMAScript 2015,是ECMAScript标准的最新版本。ES6是对于JavaScript语言的一次重大的更新，也是自从2009年ES5标准出台后的第一次更新。 ES6特性在主要的JavaScript引擎里，并[在陆续实现中](http://kangax.github.io/es5-compat-table/es6/)。

请参考[ES6 标准](http://www.ecma-international.org/ecma-262/6.0/)，其中包含了ECMAScript 6语言的全部规范说明。

ES6 包含以下新的特性：
- [arrows - 箭头函数](#arrows)
- [classes - 类](#classes)
- [enhanced object literals - 增强对象字面量](#enhanced-object-literals)
- [template strings - 模板字符串](#template-strings)
- [destructuring - 解构](#destructuring)
- [default + rest + spread - 参数默认值+不定参数+参数展开](#default--rest--spread)
- [let + const - 块级作用域](#let--const)
- [iterators + for..of - 遍历器](#iterators--forof)
- [generators](#generators)
- [unicode - 字符串扩展](#unicode)
- [modules - 模块](#modules)
- [module loaders - 模块加载](#module-loaders)
- [map + set + weakmap + weakset](#map--set--weakmap--weakset)
- [proxies - 代理](#proxies)
- [symbols - 第七种数据类型](#symbols)
- [subclassable built-ins](#subclassable-built-ins)
- [promises对象](#promises)
- [math + number + string + array + object APIs](#math--number--string--array--object-apis)
- [binary and octal literals - 二进制与八进制字面量](#binary-and-octal-literals)
- [reflect api - 反射API](#reflect-api)
- [tail calls - 尾调用](#tail-calls)

## ECMAScript 6 特性

### Arrows
箭头函数是一种采用`=>`语法的函数简写形式。这在语法上与C#，Java 8 以及CoffeeScript的相关特性很类似。箭头函数既支持声明语句体，也支持能返回表达式的值的表达式语句体。
与普通函数不同的是，箭头函数的`this`绑定的是定义时所在的对象。

```JavaScript
// 表达式语句体
var odds = evens.map(v => v + 1);
var nums = evens.map((v, i) => v + i);
var pairs = evens.map(v => ({even: v, odd: v + 1}));

// 声明语句体
nums.forEach(v => {
  if (v % 5 === 0)
    fives.push(v);
});

// 词法上的this
var bob = {
  _name: "Bob",
  _friends: [],
  printFriends() {
    this._friends.forEach(f =>
      console.log(this._name + " knows " + f));
  }
}
```

### Classes
ES6的类是对于基于原型的OO模式的一种简单的语法糖。
它有一个使得class类模式更容易使用的简便声明形式，且鼓励互操作性。类支持基于原型的继承,父调用，实例化，静态方法和构造函数。

```JavaScript
class SkinnedMesh extends THREE.Mesh {
  constructor(geometry, materials) {
    super(geometry, materials);

    this.idMatrix = SkinnedMesh.defaultMatrix();
    this.bones = [];
    this.boneMatrices = [];
    //...
  }
  update(camera) {
    //...
    super.update();
  }
  get boneCount() {
    return this.bones.length;
  }
  set matrixType(matrixType) {
    this.idMatrix = SkinnedMesh[matrixType]();
  }
  static defaultMatrix() {
    return new THREE.Matrix4();
  }
}
```

### Enhanced Object Literals
对象字面量将扩展到可以支持在设置当前对象的`prototype`对象，对于`foo: foo`赋值的缩写，定义方法，使用父调用，以及通过表达式计算（动态的）属性名。这些也使得对象字面量和类的声明的关系更加紧密了，基于对象的设计也能从这些相同的便利条件中得到一些益处。

```JavaScript
var obj = {
    // __proto__
    __proto__: theProtoObj,
    //  ‘handler: handler’的缩写
    handler,
    // 方法
    toString() {
     // Super calls
     return "d " + super.toString();
    },
    // 计算（动态的）属性名
    [ 'prop_' + (() => 42)() ]: 42
};
```

### Template Strings
模板字符串提供构造字符串的语法糖。这里的字符串插值特性与Perl, Python等语言很相似。一个标签可以随意地添加进字符串里面，使得字符串可以自定义的构造，避免了注入攻击或者从字符串内容构造更复杂的数据结构。

```JavaScript
// 基本的文字字符串创建
`In JavaScript '\n' is a line-feed.`

// 多行字符串
`In JavaScript this is
 not legal.`

// 字符串插值
var name = "Bob", time = "today";
`Hello ${name}, how are you ${time}?`

// Construct an HTTP request prefix is used to interpret the replacements and construction
GET`http://foo.org/bar?a=${a}&b=${b}
    Content-Type: application/json
    X-Credentials: ${credentials}
    { "foo": ${foo},
      "bar": ${bar}}`(myOnReadyStateChangeHandler);
```

### Destructuring
解构允许使用模式匹配进行绑定，支持数组和对象的匹配。解构具有容错性，与在标准对象`foo["bar"]`里面做查询,若找不到则返回`undefined`这种处理方式相似。

```JavaScript
// 数组匹配
var [a, , b] = [1,2,3];

// 对象匹配
var { op: a, lhs: { op: b }, rhs: c }
       = getASTNode()

// 对象匹配简写
// 绑定 `op`, `lhs` and `rhs` 进当前作用域
var {op, lhs, rhs} = getASTNode()

// 也能用在参数的位置上
function g({name: x}) {
  console.log(x);
}
g({name: 5})

// 解构的容错性
var [a] = [];
a === undefined;

// 解构具有默认值的容错性
var [a = 1] = [];
a === 1;
```

### Default + Rest + Spread
函数的参数默认值可被调用或直接进行默认求值。使用`...`Spread运算符在函数调用时可将数组转为连续的参数形式。使用`...`Rest语法可将函数尾部的多个参数转为一个数组中。Rest替代了`grguments`的需求，且在大多数场景下更为简单粗暴。

```JavaScript
function f(x, y=12) {
  // y is 12 if not passed (or passed as undefined)
  return x + y;
}
f(3) == 15
```
```JavaScript
function f(x, ...y) {
  // y is an Array
  return x * y.length;
}
f(3, "hello", true) == 6
```
```JavaScript
function f(x, y, z) {
  return x + y + z;
}
// 传递数组中的每个值作为参数
f(...[1,2,3]) == 6
```

### Let + Const
块级作用域绑定构造体。`let`是新的`var`。 `const`是单次赋值。静态的约束使得变量要在赋值之后才可使用。

```JavaScript
function f() {
  {
    let x;
    {
      // 没问题，屏蔽了作用域的名字
      const x = "sneaky";
      // 出错, const类型不能修改
      x = "foo";
    }
    // 出错，已经在块作用域中定义过了
    let x = "inner";
  }
}
```

### Iterators + For..Of
遍历器对象能够像CLR的IEnumerable接口或Java的Iterable接口一样自定义的迭代。通常我们会将`for..in`转换为自定义的基于遍历器的`for..of`迭代。不需要实现一个数组，使用像LINQ一样的惰性设计模式。

```JavaScript
let fibonacci = {
  [Symbol.iterator]() {
    let pre = 0, cur = 1;
    return {
      next() {
        [pre, cur] = [cur, pre + cur];
        return { done: false, value: cur }
      }
    }
  }
}

for (var n of fibonacci) {
  // 1000后退出
  if (n > 1000)
    break;
  console.log(n);
}
```

遍历器是基于这些鸭子类型的接口 (此处使用[TypeScript](http://typescriptlang.org)的类型语法只是来阐述问题)：
```TypeScript
interface IteratorResult {
  done: boolean;
  value: any;
}
interface Iterator {
  next(): IteratorResult;
}
interface Iterable {
  [Symbol.iterator](): Iterator
}
```

### Generators

Generators通过简单地使用`function*`和`yield`进行编写。形如`function*`的函数声明返回一个Generators实例。Generators是遍历器的子类型，遍历器包括附加的`next`和`throw`，这使得值可以回流到Generators中，所以，`yield`是一个返回（或抛出）值的表达式形式。

注意：也可以被用作类似‘await’一样的异步编程中，具体细节查看[ES7的`await`提案](http://wiki.ecmascript.org/doku.php?id=strawman:async_functions)。

```JavaScript
var fibonacci = {
  [Symbol.iterator]: function*() {
    var pre = 0, cur = 1;
    for (;;) {
      var temp = pre;
      pre = cur;
      cur += temp;
      yield cur;
    }
  }
}

for (var n of fibonacci) {
  // 1000后退出
  if (n > 1000)
    break;
  console.log(n);
}
```

Generators接口如下(此处使用 [TypeScript](http://typescriptlang.org) 的类型语法只是来阐述问题)：
```TypeScript
interface Generator extends Iterator {
    next(value?: any): IteratorResult;
    throw(exception: any);
}
```

### Unicode

渐进增强地支持全部Unicode的内容，包括字符串支持新的Unicode文本形式，也增加了新的正则表达式修饰符u来处理码位，同时，新的API可以在21bit码位级别上处理字符串，增加这些支持后可以使用 Javascript 构建全球化的应用。

```JavaScript
// same as ES5.1
"𠮷".length == 2

// 新的正则表达式行为，支持‘u’选项
"𠮷".match(/./u)[0].length == 2

// 新形式
"\u{20BB7}"=="𠮷"=="\uD842\uDFB7"

// 新的字符串操作方法
"𠮷".codePointAt(0) == 0x20BB7

// for-of遍历代码点
for(var c of "𠮷") {
  console.log(c);
}
```

### Modules

ES6 在语言层面上支持使用模块来进行组件定义，将流行的JavaScript模块加载器（AMD、CommonJS）中的模式固化到了语言中。运行时行为由宿主定义的默认加载器定义，隐式异步模型 - 直到（全部）请求的模块均可用且经处理后，才会执行（当前模块内的）代码。

```JavaScript
// lib/math.js
export function sum(x, y) {
  return x + y;
}
export var pi = 3.141593;
```
```JavaScript
// app.js
import * as math from "lib/math";
alert("2π = " + math.sum(math.pi, math.pi));
```
```JavaScript
// otherApp.js
import {sum, pi} from "lib/math";
alert("2π = " + sum(pi, pi));
```


一些额外的新特性，包括`export default`以及`export *`：

```JavaScript
// lib/mathplusplus.js
export * from "lib/math";
export var e = 2.71828182846;
export default function(x) {
    return Math.log(x);
}
```
```JavaScript
// app.js
import ln, {pi, e} from "lib/mathplusplus";
alert("2π = " + ln(e)*pi*2);
```

### Module Loaders

模块加载器支持:

- 动态加载
- 状态隔离
- 全局命名空间隔离
- 编译钩子
- 嵌套虚拟化(注: 在模块内调用模块)

默认的模块加载器是可配置的，也可以构建新的加载器，对在隔离和受限上下文中的代码进行求值和加载。

```JavaScript
// 动态加载 – ‘System’ 是默认加载器
System.import('lib/math').then(function(m) {
  alert("2π = " + m.sum(m.pi, m.pi));
});

// 创建执行沙箱 – new Loaders
var loader = new Loader({
  global: fixup(window) // replace ‘console.log’
});
loader.eval("console.log('hello world!');");

// 直接操作模块缓存
System.get('jquery');
System.set('jquery', Module({$: $})); // WARNING: not yet finalized
```

### Map + Set + WeakMap + WeakSet

用于实现常见算法的高效数据结构，WeakMaps提供不会泄露的对象键(对象作为键名，而且键名指向对象)索引表。

```JavaScript
// Sets
var s = new Set();
s.add("hello").add("goodbye").add("hello");
s.size === 2;
s.has("hello") === true;

// Maps
var m = new Map();
m.set("hello", 42);
m.set(s, 34);
m.get(s) == 34;

// Weak Maps
var wm = new WeakMap();
wm.set(s, { extra: 42 });
wm.size === undefined

// Weak Sets
var ws = new WeakSet();
ws.add({ data: 42 });
// 由于心加进去的对象没有其他的引用，它讲不会在set中被继续保留
```

### Proxies

代理可以创造一个具备宿主对象全部可用行为的对象。可用于拦截、对象虚拟化、日志/分析等。

```JavaScript
// 代理一个普通对象
var target = {};
var handler = {
  get: function (receiver, name) {
    return `Hello, ${name}!`;
  }
};

var p = new Proxy(target, handler);
p.world === 'Hello, world!';
```

```JavaScript
// 代理一个函数对象
var target = function () { return 'I am the target'; };
var handler = {
  apply: function (receiver, ...args) {
    return 'I am the proxy';
  }
};

var p = new Proxy(target, handler);
p() === 'I am the proxy';
```

所有运行时级别的元操作都有对应的陷阱（使得这些操作都可以被代理）：

```JavaScript
var handler =
{
  get:...,
  set:...,
  has:...,
  deleteProperty:...,
  apply:...,
  construct:...,
  getOwnPropertyDescriptor:...,
  defineProperty:...,
  getPrototypeOf:...,
  setPrototypeOf:...,
  enumerate:...,
  ownKeys:...,
  preventExtensions:...,
  isExtensible:...
}
```

### Symbols

Symbols 能够实现针对对象状态的访问控制，允许使用`string`(与ES5相同)或`symbol`作为键来访问属性。Symbols是一个新的原始类型，可选的`description`参数可以用于调试——但并不是符号身份的一部分。Symbols是独一无二的(如同gensym（所产生的符号）)，但不是私有的，因为它们可以通过类似`Object.getOwnPropertySymbols`的反射特性暴露出来。


```JavaScript
var MyClass = (function() {

  // module scoped symbol
  var key = Symbol("key");

  function MyClass(privateData) {
    this[key] = privateData;
  }

  MyClass.prototype = {
    doStuff: function() {
      ... this[key] ...
    }
  };

  return MyClass;
})();

var c = new MyClass("hello")
c["key"] === undefined
```

### Subclassable Built-ins

在 ES6 中，内建对象，如Array、Date以及DOM元素可以被子类化。

针对名为Ctor的函数，其对应的对象的构造现在分为两个阶段（这两个阶段都使用虚分派）：

*调用Ctor[@@create]为对象分配空间，并插入特殊的行为
*在新实例上调用构造函数来进行初始化

已知的@@create符号可以通过Symbol.create来使用，内建对象现在显式暴露它们的@@create。

```JavaScript
// 数组伪代码
class Array {
    constructor(...args) { /* ... */ }
    static [Symbol.create]() {
        // Install special [[DefineOwnProperty]]
        // to magically update 'length'
    }
}

// 用户代码实现数组的子对象
class MyArray extends Array {
    constructor(...args) { super(...args); }
}

// 两步实现 'new':
// 1) 调用 @@create 来分配对象
// 2) 在新的实例上调用构造函数
var arr = new MyArray();
arr[1] = 12;
arr.length == 2
```

### Math + Number + String + Array + Object APIs

新加入了许多库，包括核心数学库，进行数组转换的协助函数，字符串 helper，以及用来进行拷贝的Object.assign。

```JavaScript
Number.EPSILON
Number.isInteger(Infinity) // false
Number.isNaN("NaN") // false

Math.acosh(3) // 1.762747174039086
Math.hypot(3, 4) // 5
Math.imul(Math.pow(2, 32) - 1, Math.pow(2, 32) - 2) // 2

"abcde".includes("cd") // true
"abc".repeat(3) // "abcabcabc"

Array.from(document.querySelectorAll('*')) // 返回一个真实的数组
Array.of(1, 2, 3) // 类似于new Array(...), 但不支持特殊的单参数行为
[0, 0, 0].fill(7, 1) // [0,7,7]
[1, 2, 3].find(x => x == 3) // 3
[1, 2, 3].findIndex(x => x == 2) // 1
[1, 2, 3, 4, 5].copyWithin(3, 0) // [1, 2, 3, 1, 2]
["a", "b", "c"].entries() // iterator [0, "a"], [1,"b"], [2,"c"]
["a", "b", "c"].keys() // iterator 0, 1, 2
["a", "b", "c"].values() // iterator "a", "b", "c"

Object.assign(Point, { origin: new Point(0,0) })
```

### Binary and Octal Literals

加入对二进制(`b`)和八进制(`o`)字面量的支持。

```JavaScript
0b111110111 === 503 // true
0o767 === 503 // true
```

### Promises

Promise是用来进行异步编程的库。Promise是对一个“将来可能会变得可用”的值的第一类表示，Promise被使用在现有的许多JavaScript库中。

```JavaScript
function timeout(duration = 0) {
    return new Promise((resolve, reject) => {
        setTimeout(resolve, duration);
    })
}

var p = timeout(1000).then(() => {
    return timeout(2000);
}).then(() => {
    throw new Error("hmm");
}).catch(err => {
    return Promise.all([timeout(100), timeout(200)]);
})
```

### Reflect API

完整的反射API在对象上暴露了运行时级别的元操作，从效果上来说，这是一个反代理API，并允许调用与代理陷阱中相同的元操作。实现代理非常有用。

```JavaScript
// 尚无示例代码
```

### Tail Calls

保证尾部调用时栈不会无限增长，这使得递归算法在面对未作限制的输入时，能够安全地执行。

```JavaScript
function factorial(n, acc = 1) {
    'use strict';
    if (n <= 1) return acc;
    return factorial(n - 1, n * acc);
}

// 栈溢出存在于现在绝大多数的实现中，
// 但是在 ES6 中，针对任意的输入都很安全
factorial(100000)
```
