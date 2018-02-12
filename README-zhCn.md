# [ECMAScript 6 特性](https://github.com/lukehoban/es6features)

[es6features中文版](https://github.com/taoqianbao/es6features)

## 介绍

ECMAScript 6，也被称做ECMAScript 2015，是ECMAScript标准的下一个版本。这个标准预计将于2015年6月被正式批准。ES6是这门语言的一次重大更新，自ES5以来，该语言的首次更新是在2009年。主流Javascript引擎对ES6相关特性的实现也[正在进行中](http://kangax.github.io/es5-compat-table/es6/)。

前往[ES6标准草案](https://people.mozilla.org/~jorendorff/es6-draft.html)查看ECMAScript 6的所有细节

ES6包括以下特性
- [arrows 箭头函数](#arrows)
- [classes 类](#classes)
- [enhanced object literals 增强的对象字面量](#enhanced-object-literals)
- [template strings 模板字符串](#template-strings)
- [destructuring 解构](#destructuring)
- [default + rest + spread 关键字](#default--rest--spread)
- [let + const 关键字](#let--const)
- [iterators + for...of 遍历](#iterators--forof)
- [generators 生成器](#generators)
- [unicode 统一码](#unicode)
- [modules 模块](#modules)
- [module loaders 模块加载器](#module-loaders)
- [map + set + weakmap + weakset 新的数据类型](#map--set--weakmap--weakset)
- [proxies 代理](#proxies)
- [symbols 符号](#symbols)
- [subclassable built-ins 内置的继承](#subclassable-built-ins)
- [promises 承诺](#promises)
- [math + number + string + array + object APIs 新增的一些数据类型方法](#math--number--string--array--object-apis)
- [binary and octal literals 二进制和八进制字面量](#binary-and-octal-literals)
- [reflect api 反射](#reflect-api)
- [tail calls 尾调用](#tail-calls)

## ECMAScript 6 特性

### Arrows

#### 箭头函数

箭头函数是使用 => 语法简写的函数。在语法上类似C#、Java 8和CoffeeScript中对应的特性。他们同时支持表达式和语句块。和普通函数不同的是，箭头函数和上下文代码共享同一个词法this

```Javascipt
// Expression bodies 表达式
var odds = evens.map(v => v + 1);
var nums = evens.map((v, i) => v + i);
var pairs = evens.map(v => ({even: v, odd: v + 1}));

// Statement bodies 语句块
nums.forEach(v => {
  if (v % 5 === 0)
    fives.push(v);
});

// Lexical this this语法
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

#### 类

ES6中提供了一个基于原型的面向对象模式的语法糖。简单的声明方式使得类模式变得更容易使用，增加了类的互用性。类支持原型继承、父方法调用、实例方法、静态方法和构造函数。

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

#### 增强的对象字面量

对象字面量扩展了以下特性，支持在构造时设置原型，`foo: foo`赋值的简写，方法定义，调用父方法，使用表达式计算属性名。同时这些也使得对象字面量和类声明更接近，基于对象的设计也在这种便利中受益。

```JavaScript
var obj = {
    // __proto__
    __proto__: theProtoObj,
    // Shorthand for ‘handler: handler’
    handler,
    // Methods
    toString() {
     // Super calls
     return "d " + super.toString();
    },
    // Computed (dynamic) property names
    [ 'prop_' + (() => 42)() ]: 42
};
```

### Template Strings

#### 模板字符串

模板字符串提供了构建字符串的语法糖。这类似于Perl、Python和其他语言中的字符串插值特性。此外，作为可选项，使用标签可以自定义字符串的构建行为，避免注入攻击，或者基于字符串构建高阶的数据结构。

```JavaScript
// 基本的字面量字符串创建
`In JavaScript '\n' is a line-feed.`

// 多行文本
`In JavaScript this is
 not legal.`

// 字符串插值
var name = "Bob", time = "today";
`Hello ${name}, how are you ${time}?`

//构造一个HTTP请求，其中的GET前缀(译者注：即上文提到的标签)用来处理替换和构造逻辑
GET`http://foo.org/bar?a=${a}&b=${b}
    Content-Type: application/json
    X-Credentials: ${credentials}
    { "foo": ${foo},
      "bar": ${bar}}`(myOnReadyStateChangeHandler);
```

### Destructuring

#### 解构

解构允许使用模式匹配的绑定，支持数组和对象。解构是Fail-soft的，类似于对象的查找过程`foo["bar"]`，未找到则会对应undefined

```JavaScript
// 数组匹配
var [a, , b] = [1,2,3];

// 对象匹配
var { op: a, lhs: { op: b }, rhs: c }
       = getASTNode()

// 对象匹配的简写
// 绑定`op`，`lhs`，`rhs`到作用域中
var {op, lhs, rhs} = getASTNode()

// 也能被用在形参中
function g({name: x}) {
  console.log(x);
}
g({name: 5})

// Fail-soft的解构
var [a] = [];
a === undefined;

// 带默认值的Fail-soft解构
var [a = 1] = [];
a === 1;
```

### Default + Rest + Spread

#### 默认参数 + 剩余参数 + 参数展开

支持被调用函数设置参数的默认值。在调用函数时使用`...`可以将一个数组展开后作为参数传入。在定义函数时使用`...`可以将传入的剩余参数转化成一个数组。剩余参数取代了`arguments`的使用，它能以更直接的方式处理大多数问题。

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

### Let + Const

#### let和const关键字

let和const都是块级作用域的声明方式。`let`是新的`var`。`const`是单次赋值的。`const`的静态限制禁止变量在赋值前使用。

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

### Iterators + For..Of

#### 迭代器和For..Of

Iterator对象让javascript拥有了像CLR IEnumerable和Java Iterable一样自定义迭代器的能力。将`for..in`转换成基于迭代器的自定义遍历的`for..of`形式。不需要实现一个类似LINQ中惰性设计模式的数组。

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

Iteration基于[鸭子类型](https://en.wikipedia.org/wiki/Duck_typing)的接口(以下使用[TypeScript](http://typescriptlang.org)的语法，仅供解释用)

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

#### 生成器

Generators使用`function*`和`yield`的语法简化了迭代器的书写。一个使用`function*`声明的函数返回一个Generator实例。Generators也是迭代器的一种，但它拥有额外的`next`和`throw`方法。这允许值回到generator中，所以`yield`是一种返回（或抛出）值的表达式形式。

注意：可以用它来进行类似‘await’的异步编程，具体可以查看ES7的`await`提案

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

generator接口（这里只是使用[TypeScript](http://typescriptlang.org)的类型语法来作说明）

```TypeScript
interface Generator extends Iterator {
    next(value?: any): IteratorResult;
    throw(exception: any);
}
```

### Unicode
TODO

### Modules

#### 模块

ES支持从语言层面上使用模块进行组件定义。写法来自流行的Javascript模块加载器(AMD，CommonJS)。运行时的行为由宿主的加载器定义。内部使用了隐式的异步模型 - 在依赖的模块不可用或没处理前，当前模块的代码不会执行

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

增加了`export default`和`export *`这些额外特性

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

#### 模块加载器

模块加载器支持：
- 动态加载
- 状态隔离
- 全局命名空间隔离
- 编译钩子
- 嵌套虚拟化

默认的加载器可配置，也能构造新的加载器在隔离和约束的上下文中进行代码的执行和加载。

```JavaScript
// 动态加载 – ‘System’是默认加载器
System.import('lib/math').then(function(m) {
  alert("2π = " + m.sum(m.pi, m.pi));
});

// 创建执行的沙盒 – 新的加载器
var loader = new Loader({
  global: fixup(window) // replace ‘console.log’
});
loader.eval("console.log('hello world!');");

// 直接操作模块的缓存
System.get('jquery');
System.set('jquery', Module({$: $})); // 警告：尚未完成
```

### Map + Set + WeakMap + WeakSet
TODO

### Proxies
TODO

### Symbols
TODO

### Subclassable Built-ins
TODO

### Math + Number + String + Array + Object APIs
很多新库的加入，包括Math核心库，数组转化助手，字符串助手，还有用来拷贝的Object.assign

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
[1, 2, 3].find(x => x == 3) // 3
[1, 2, 3].findIndex(x => x == 2) // 1
[1, 2, 3, 4, 5].copyWithin(3, 0) // [1, 2, 3, 1, 2]
["a", "b", "c"].entries() // iterator [0, "a"], [1,"b"], [2,"c"]
["a", "b", "c"].keys() // iterator 0, 1, 2
["a", "b", "c"].values() // iterator "a", "b", "c"

Object.assign(Point, { origin: new Point(0,0) })
```

### Binary and Octal Literals
给binary (`b`)和octal (`o`)增加了两种新的数字字面量形式

```JavaScript
0b111110111 === 503 // true
0o767 === 503 // true
```

### Promises

Promise是用于异步编程的库。Promise作为将来可能获取到的值的容器。它已经被使用于很多现有的类库当中。

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

#### 反射

整个反射接口暴露了对象在运行时级别的元操作。这其实和Proxy刚好相反，它允许在proxy捕获时调用与Proxy接口相对应的元操作。在实现proxies时尤其有用。

```JavaScript
// No sample yet
```

### Tail Calls

#### 尾调用

保证尾调用不会导致栈空间无限制的增长。使得在没有限制输入的时候递归算法也能保证安全。

```JavaScript
function factorial(n, acc = 1) {
    'use strict';
    if (n <= 1) return acc;
    return factorial(n - 1, n * acc);
}

// 在目前大多数实现中会导致栈溢出,
// 但在es6中没有限制的输入也是安全的
factorial(100000)
```
