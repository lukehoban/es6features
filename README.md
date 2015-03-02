# ECMAScript 6 Features 中文译版

第一次翻译 Ecmascript 相关文档，如果词不达意欢迎提 issue，我多多改进
为保证理解的顺畅，我采用中英混排的方式进行译制，如有不解，请查看对应原文

翻译完之后最大的感受是，语言进化到现阶段有了许多成熟的方案，例如，接口，duck-typed等，还有许多乍一看都不怎么了解的概念，各个语言都争相加入这些语言设计的新方案，所以 ES6 的一部分特性看起来跟 Go 真的蛮像的。

## Introduction 简介
ECMAScript 6 is the upcoming version of the ECMAScript standard. This standard is targeting ratification in June 2015. ES6 is a significant update to the language, and the first update to the language since ES5 was standardized in 2009. Implementation of these features in major JavaScript engines is [underway now](http://kangax.github.io/es5-compat-table/es6/).

ECMAScript 6 是 ECMAScript 的下一代标准，预计将在 2015年6月 正式发布。ES6 的发布将是是这门语言自 2009 年 ES5 正式发布以来的首次更新，是一次富有意义的更新。Javascript核心引擎的[新特性](http://kangax.github.io/es5-compat-table/es6/)仍然在快速开发中。

See the [draft ES6 standard](https://people.mozilla.org/~jorendorff/es6-draft.html) for full specification of the ECMAScript 6 language.

这里有[ES6标准草案](https://people.mozilla.org/~jorendorff/es6-draft.html)的所有细节可以参考

ES6 includes the following new features:

ES6 的具体特性如下：

- [Arrows 箭头函数](#arrows)
- [classes 类](#classes)
- [enhanced object literals 增强的对象字面量](#enhanced-object-literals)
- [template strings 模板字符串](#template-strings)
- [destructuring 解构](#destructuring)
- [default + rest + spread 默认值+多余参数组合+参数伸展](#default--rest--spread)
- [let + const let + const 操作符](#let--const)
- [iterators + for..of 迭代器 + for...of](#iterators--forof)
- [generators 生成器](#generators)
- [unicode](#unicode)
- [modules](#modules)
- [module loaders](#module-loaders)
- [map + set + weakmap + weakset](#map--set--weakmap--weakset)
- [proxies](#proxies)
- [symbols](#symbols)
- [subclassable built-ins](#subclassable-built-ins)
- [promises](#promises)
- [math + number + string + object APIs](#math--number--string--object-apis)
- [binary and octal literals](#binary-and-octal-literals)
- [reflect api](#reflect-api)
- [tail calls](#tail-calls)

## ECMAScript 6 Features 特性

### Arrows 箭头函数
Arrows are a function shorthand using the `=>` syntax. They are syntactically similar to the related feature in C#, Java 8 and CoffeeScript. They support both expression and statement bodies. Unlike functions, arrows share the same lexical this as their surrounding code.

箭头函数是形如`=>`的函数简写形式，在语法上与 C#、Java 8 和 CoffeScript 非常相似，它们同时支持表达式和语句体，与function定义的函数所不同的是，箭头函数在上下文中共享相同的关键字`this`

```JavaScript
// Expression bodies
// 表达式
var odds = evens.map(v => v + 1);
var nums = evens.map((v, i) => v + i);
var pairs = evens.map(v => ({even: v, odd: v + 1}));

// Statement bodies
// 语句体
nums.forEach(v => {
  if (v % 5 === 0)
    fives.push(v);
});

// Lexical this
// this 关键字
var bob = {
  _name: "Bob",
  _friends: ["Amy", "Bob", "Cinne", "Dylan", "Ellen"],
  printFriends() {
    this._friends.forEach(f =>
      console.log(this._name + " knows " + f));
  }
}
```

### Classes 类
ES6 classes are a simple sugar over the prototype-based OO pattern. Having a single convenient declarative form makes class patterns easier to use, and encourages interoperability. Classes support prototype-based inheritance, super calls, instance and static methods and constructors.

ES6 的类是基于原型的面向对象模式的一个简单的语法糖，它有一个便捷的声明形式，并鼓励互操作性，这使得类模式更容易使用。class定义的类支持基于原型的继承、[SuperCalls](http://en.wikipedia.org/wiki/Call_super)、实例和静态方法以及构造函数。

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
  static defaultMatrix() {
    return new THREE.Matrix4();
  }
}
```

### Enhanced Object Literals 增强的Object字面量
Object literals are extended to support setting the prototype at construction, shorthand for foo: foo assignments, defining methods, making super calls, and computing property names with expressions. Together, these also bring object literals and class declarations closer together, and let object-based design benefit from some of the same conveniences.

Object字面量被扩展以支持以下特性：在构建的时候设置原型、`foo: foo`的简写形式赋值、定义方法、调用[Super Calls](http://en.wikipedia.org/wiki/Call_super)、计算表达式的属性名称等。这样就使得Object字面量和类的声明的联系更加紧密，使得基于对象的设计更加便利

```JavaScript
var obj = {
    // __proto__
    __proto__: theProtoObj,
    // Shorthand for ‘handler: handler’
    // ‘handler: handler’ 的简写形式
    handler,
    // Methods
    toString() {
      // Super calls
      return "d " + super.toString();
    },
    // Computed (dynamic) property names
    // 动态计算属性名称
    [ 'prop_' + (() => 42)() ]: 42
};
```

### Template Strings 模板字符串
Template strings provide syntactic sugar for constructing strings. This is similar to string interpolation features in Perl, Python and more. Optionally, a tag can be added to allow the string construction to be customized, avoiding injection attacks or constructing higher level data structures from string contents.

模板字符串提供构造字符串的语法糖，这与Perl、Python等许多语言中的字符串插值功能非常相似，你也可以通过添加标签(tag)来自定义构造字符串，避免注入攻击，或者基于字符串构建更高层次的数据结构。

```JavaScript
// Basic literal string creation
// 基础字符串字面量的创建
`In JavaScript '\n' is a line-feed.`

// Multiline strings
// 多行字符串
`In JavaScript this is
 not legal.`

 // String interpolation
// 字符串插值
var name = "Bob", time = "today";
`Hello ${name}, how are you ${time}?`

// Construct an HTTP request prefix is used to interpret the replacements and construction
// 构造一个HTTP请求前缀用来解释替换和构造，大意就是可以构造一个通用的HTTP prefix并通过赋值生成最终的HTTP请求
GET`http://foo.org/bar?a=${a}&b=${b}
    Content-Type: application/json
    X-Credentials: ${credentials}
    { "foo": ${foo},
      "bar": ${bar}}`(myOnReadyStateChangeHandler);
```

### Destructuring 解构
Destructuring allows binding using pattern matching, with support for matching arrays and objects.  Destructuring is fail-soft, similar to standard object lookup `foo["bar"]`, producing `undefined` values when not found.

解构允许结合使用模式匹配，支持匹配数组和对象，解构支持[失效弱化](http://www.computerhope.com/jargon/f/failsoft.htm)，与标准的对象查询`foo["bar"]`相似，当查询无结果时生成`undefined`值

```JavaScript
// list matching
// 列表匹配
var [a, , b] = [1,2,3];

// object matching
// 对象匹配
var { op: a, lhs: { op: b }, rhs: c }
       = getASTNode()

// object matching shorthand
// binds `op`, `lhs` and `rhs` in scope
// 对象匹配简写形式
var {op, lhs, rhs} = getASTNode()

// 上面作者给的示例看得云里雾里的，这里我再给出一个
function today() { return { d: 2, m: 3, y: 2015 }; }
var { m: month, y: year } = today(); // month = 3, year = 2015

// Can be used in parameter position
// 也可以作为参数使用
function g({name: x}) {
  console.log(x);
}
g({name: 5})

// Fail-soft destructuring
// 失效弱化解构，结果查询不到时定义为 undefined
var [a] = [];
a === undefined;

// Fail-soft destructuring with defaults
// 具备默认值的失效弱化解构
var [a = 1] = [];
a === 1;
```

### Default + Rest + Spread  默认值+多余参数组合+参数伸展
Callee-evaluated default parameter values.  Turn an array into consecutive arguments in a function call.  Bind trailing parameters to an array.  Rest replaces the need for `arguments` and addresses common cases more directly.

本人英语烂，直译出来惨不忍睹，尝试意译一下，欢迎issue里给出直译参考（泪目

1 首先，参数可以指定默认值
2 其次，可以通过...运算符将尾随参数转换为一个数组
3 最后，同样通过...运算符将作为参数的数组拆解为相应参数变量

果真只能靠自己~早已被作者虐哭

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
// Pass each elem of array as argument
f(...[1,2,3]) == 6
```

### Let + Const 操作符
Block-scoped binding constructs.  `let` is the new `var`.  `const` is single-assignment.  Static restrictions prevent use before assignment.

let 和 const 属于块级作用域的绑定构造，`let` 是新的 `var`，只在块级作用域内有效，`const` 是[单赋值](http://zh.wikipedia.org/zh-cn/%E9%9D%99%E6%80%81%E5%8D%95%E8%B5%8B%E5%80%BC%E5%BD%A2%E5%BC%8F)，声明的是块级作用域的常量，静态限制在赋值之前禁止使用


```JavaScript
function f() {
  {
    let x;
    {
      // okay, block scoped name
      const x = "sneaky";
      // error, const
      x = "foo";
    }
    // error, already declared in block
    let x = "inner";
  }
}
```

### Iterators + For..Of 迭代器 + For..of 循环
Iterator objects enable custom iteration like CLR IEnumerable or Java Iterable.  Generalize `for..in` to custom iterator-based iteration with `for..of`.  Don’t require realizing an array, enabling lazy design patterns like LINQ.

迭代器对象允许像 [CLI IEnumerable](https://msdn.microsoft.com/zh-cn/library/system.collections.ienumerable(v=vs.110).aspx) 或者 [Java Iterable](http://docs.oracle.com/javase/7/docs/api/java/lang/Iterable.html) 一样自定义迭代器。将`for..in`转换为自定义的基于迭代器的形如`for..of`的迭代，不需要实现一个数组，支持像 [LINQ](https://msdn.microsoft.com/zh-cn/library/bb397926.aspx) 一样的惰性设计模式
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
  // truncate the sequence at 1000
  if (n > 1000)
    break;
  console.log(n);
}
```

Iteration is based on these duck-typed interfaces (using [TypeScript](http://typescriptlang.org) type syntax for exposition only):
迭代器基于这些[鸭子类型的接口](http://zh.wikipedia.org/zh/%E9%B8%AD%E5%AD%90%E7%B1%BB%E5%9E%8B) (仅使用[TypeScript](http://typescriptlang.org) 类型的句法阐述问题)：
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

### Generators 生成器
Generators simplify iterator-authoring using `function*` and `yield`.  A function declared as function* returns a Generator instance.  Generators are subtypes of iterators which include additional  `next` and `throw`.  These enable values to flow back into the generator, so `yield` is an expression form which returns a value (or throws).

生成器通过使用`function*`和`yield`简化迭代器的编写， 形如function*的函数声明返回一个生成器实例，生成器是迭代器的子类型，迭代器包括附加的`next`和`throw`，这使得值可以回流到生成器中，`yield`是一个返回或抛出一个值的表达式形式。

Note: Can also be used to enable ‘await’-like async programming, see also ES7 `await` proposal.
注意：也可以被用作类似‘await’一样的异步编程中，具体细节查看[ES7的`await`提案](http://wiki.ecmascript.org/doku.php?id=strawman:async_functions)

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
  // truncate the sequence at 1000
  if (n > 1000)
    break;
  console.log(n);
}
```

The generator interface is (using [TypeScript](http://typescriptlang.org) type syntax for exposition only):
生成器接口如下(仅使用[TypeScript](http://typescriptlang.org) 类型的句法阐述问题)：

```TypeScript
interface Generator extends Iterator {
    next(value?: any): IteratorResult;
    throw(exception: any);
}
```

### Unicode
Non-breaking additions to support full Unicode, including new Unicode literal form in strings and new RegExp `u` mode to handle code points, as well as new APIs to process strings at the 21bit code points level.  These additions support building global apps in JavaScript.

```JavaScript
// same as ES5.1
"𠮷".length == 2

// new RegExp behaviour, opt-in ‘u’
"𠮷".match(/./u)[0].length == 2

// new form
"\u{20BB7}"=="𠮷"=="\uD842\uDFB7"

// new String ops
"𠮷".codePointAt(0) == 0x20BB7

// for-of iterates code points
for(var c of "𠮷") {
  console.log(c);
}
```

### Modules
Language-level support for modules for component definition.  Codifies patterns from popular JavaScript module loaders (AMD, CommonJS). Runtime behaviour defined by a host-defined default loader.  Implicitly async model – no code executes until requested modules are available and processed.

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

Some additional features include `export default` and `export *`:

```JavaScript
// lib/mathplusplus.js
export * from "lib/math";
export var e = 2.71828182846;
export default function(x) {
    return Math.exp(x);
}
```
```JavaScript
// app.js
import exp, {pi, e} from "lib/mathplusplus";
alert("2π = " + exp(pi, e));
```

### Module Loaders
Module loaders support:
- Dynamic loading
- State isolation
- Global namespace isolation
- Compilation hooks
- Nested virtualization

The default module loader can be configured, and new loaders can be constructed to evaluate and load code in isolated or constrained contexts.

```JavaScript
// Dynamic loading – ‘System’ is default loader
System.import('lib/math').then(function(m) {
  alert("2π = " + m.sum(m.pi, m.pi));
});

// Create execution sandboxes – new Loaders
var loader = new Loader({
  global: fixup(window) // replace ‘console.log’
});
loader.eval("console.log('hello world!');");

// Directly manipulate module cache
System.get('jquery');
System.set('jquery', Module({$: $})); // WARNING: not yet finalized
```

### Map + Set + WeakMap + WeakSet
Efficient data structures for common algorithms.  WeakMaps provides leak-free object-key’d side tables.

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
// Because the added object has no other references, it will not be held in the set
```

### Proxies
Proxies enable creation of objects with the full range of behaviors available to host objects.  Can be used for interception, object virtualization, logging/profiling, etc.

```JavaScript
// Proxying a normal object
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
// Proxying a function object
var target = function () { return 'I am the target'; };
var handler = {
  apply: function (receiver, ...args) {
    return 'I am the proxy';
  }
};

var p = new Proxy(target, handler);
p() === 'I am the proxy';
```

There are traps available for all of the runtime-level meta-operations:

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
Symbols enable access control for object state.  Symbols allow properties to be keyed by either `string` (as in ES5) or `symbol`.  Symbols are a new primitive type. Optional `name` parameter used in debugging - but is not part of identity.  Symbols are unique (like gensym), but not private since they are exposed via reflection features like `Object.getOwnPropertySymbols`.


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
In ES6, built-ins like `Array`, `Date` and DOM `Element`s can be subclassed.

Object construction for a function named `Ctor` now uses two-phases (both virtually dispatched):
- Call `Ctor[@@create]` to allocate the object, installing any special behavior
- Invoke constructor on new instance to initialize

The known `@@create` symbol is available via `Symbol.create`.  Built-ins now expose their `@@create` explicitly.

```JavaScript
// Pseudo-code of Array
class Array {
    constructor(...args) { /* ... */ }
    static [Symbol.create]() {
        // Install special [[DefineOwnProperty]]
        // to magically update 'length'
    }
}

// User code of Array subclass
class MyArray extends Array {
    constructor(...args) { super(...args); }
}

// Two-phase 'new':
// 1) Call @@create to allocate object
// 2) Invoke constructor on new instance
var arr = new MyArray();
arr[1] = 12;
arr.length == 2
```

### Math + Number + String + Object APIs
Many new library additions, including core Math libraries, Array conversion helpers, and Object.assign for copying.

```JavaScript
Number.EPSILON
Number.isInteger(Infinity) // false
Number.isNaN("NaN") // false

Math.acosh(3) // 1.762747174039086
Math.hypot(3, 4) // 5
Math.imul(Math.pow(2, 32) - 1, Math.pow(2, 32) - 2) // 2

"abcde".includes("cd") // true
"abc".repeat(3) // "abcabcabc"

Array.from(document.querySelectorAll('*')) // Returns a real Array
Array.of(1, 2, 3) // Similar to new Array(...), but without special one-arg behavior
[0, 0, 0].fill(7, 1) // [0,7,7]
[1,2,3].findIndex(x => x == 2) // 1
["a", "b", "c"].entries() // iterator [0, "a"], [1,"b"], [2,"c"]
["a", "b", "c"].keys() // iterator 0, 1, 2
["a", "b", "c"].values() // iterator "a", "b", "c"

Object.assign(Point, { origin: new Point(0,0) })
```

### Binary and Octal Literals
Two new numeric literal forms are added for binary (`b`) and octal (`o`).

```JavaScript
0b111110111 === 503 // true
0o767 === 503 // true
```

### Promises
Promises are a library for asynchronous programming.  Promises are a first class representation of a value that may be made available in the future.  Promises are used in many existing JavaScript libraries.

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
Full reflection API exposing the runtime-level meta-operations on objects.  This is effectively the inverse of the Proxy API, and allows making calls corresponding to the same meta-operations as the proxy traps.  Especially useful for implementing proxies.

```JavaScript
// No sample yet
```

### Tail Calls
Calls in tail-position are guaranteed to not grow the stack unboundedly.  Makes recursive algorithms safe in the face of unbounded inputs.

```JavaScript
function factorial(n, acc = 1) {
    'use strict';
    if (n <= 1) return acc;
    return factorial(n - 1, n * acc);
}

// Stack overflow in most implementations today,
// but safe on arbitrary inputs in ES6
factorial(100000)
```
