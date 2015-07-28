# ECMAScript 6 <sup>[git.io/es6features](http://git.io/es6features)</sup>

## Εισαγωγή
Η ECMAScript 6, επίσης γνωστή και ως ECMAScript 2015, είναι η ποιο πρόσφατη έκδοση του προτύπου ECMAScript. Η ES6 αποτελεί μια σημαντική αναβάθμιση της γλώσσας, και η πρώτη αναβάθμιση στην γλώσσα από την ES5 τυποποιήθηκε το 2009. Υλοποίηση αυτών των χαρακτηριστικών στις σημαντικές μηχανές JavaScript είναι [τώρα σε εξέλιξη](http://kangax.github.io/es5-compat-table/es6/).

Δείτε το [πρότυπο ES6](http://www.ecma-international.org/ecma-262/6.0/) για πλήρη αναφορά στις προδιαγραφές της γλώσσας ECMAScript 6.

Η ES6 περιλαμβάνει τα ακόλουθα νέα χαρακτηριστικά:
- [βέλη (arrows)](#arrows)
- [κλάσεις (classes)](#classes)
- [βελτιομένα κυριολεκτικά αντικειμένων (enhanced object literals)](#enhanced-object-literals)
- [πρότυπα συμβολοσειρών (template strings)](#template-strings)
- [αποδόμηση (destructuring)](#destructuring)
- [default + rest + spread](#default--rest--spread)
- [let + const](#let--const)
- [επαναλήπτες + for..of (iterators + for..of)](#iterators--forof)
- [γεννήτριες (generators)](#generators)
- [unicode](#unicode)
- [μονάδες (modules)](#modules)
- [φορτωτές μονάδων (module loaders)](#module-loaders)
- [map + set + weakmap + weakset](#map--set--weakmap--weakset)
- [proxies](#proxies)
- [σύμβολα (symbols)](#symbols)
- [subclassable built-ins](#subclassable-built-ins)
- [υποσχέσεις (promises)](#promises)
- [APIs για μαθηματικά + αριθμούς + συμβολοσειρές + πίνακες + αντικείμενα (math + number + string + array + object APIs)](#math--number--string--array--object-apis)
- [κυριολεκτικά δυαδικών και οκταδικών (binary and octal literals)](#binary-and-octal-literals)
- [API ανάκλασης (reflect api)](#reflect-api)
- [κλήσεις ουράς (tail calls)](#tail-calls)

## Χαρακτηριστικά της ECMAScript 6

### Βέλη (arrow)
Τα βέλη είναι συντομογραφίες συναρτήσεων που χρησιμοποιούν το συντακτικό `=>`. Είναι συντακτικά όμοια των σχετικών χαρακτηριστικών στην C#, Java 8 και της CoffeeScript. Υποστηρίζουν τόσο δηλώσεις κορμού σε πλαίσιο όσο και δηλώσεις κορμού οι οποίες επιστρέφουν την τιμή της δήλωσης. Αντίθετα από τις συναρτήσεις, τα βέλη μοιράζονται το ίδιο λεξιλογικό `this` με αυτό του κώδικα που τα πλαισιώνει.  

```JavaScript
// Κορμοί δήλωσης
var odds = evens.map(v => v + 1);
var nums = evens.map((v, i) => v + i);
var pairs = evens.map(v => ({even: v, odd: v + 1}));

// Κορμοί δήλωσης
nums.forEach(v => {
  if (v % 5 === 0)
    fives.push(v);
});

// Λεξιλογικό this
var bob = {
  _name: "Bob",
  _friends: [],
  printFriends() {
    this._friends.forEach(f =>
      console.log(this._name + " knows " + f));
  }
}
```

### Κλάσεις (Classes)
Η κλάσεις στην ES6, είναι απλή ζάχαρη πάνω από τα βασισμένα σε prototypes αντικειμενοστρεφή πρότυπα. Η ύπαρξη μιας ενιαίας βολικής μορφής δήλωσης, κάνει τα πρότυπα κλάσεων ποιο εύκολα στην χρήση, και προτρέπουν την διαλειτουργικότητα. Οι κλάσεις υποστηρίζουν κληρονομικότητα βασισμένη στα prototypes, κλήσεις super, στιγμιότυπα, στατικές μεθόδους και δημιουργούς.

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

### Βελτιομένα κυριολεκτικά αντικειμένων (enhanced object literals)
Τα κυριολεκτικά αντικειμένων έχουν επεκταθεί ώστε να υποστηρίζουν την ρύθμιση του prototype κατά την δημιουργία, συντομεύσεις για αναθέσεις `foo: foo`, ορισμούς μεθόδων, κλήσεις super, και υπολογισμό ονομάτων ιδιοτήτων με εκφράσεις. Μαζί, αυτά επίσης φέρνουν κυριολεκτικά αντικείμενα και δηλώσεις κλάσεων ποιο κοντά μαζί, και επιτρέπουν τα πλεονεκτήματα του βασισμένου σε αντικείμενα σχεδιασμού μερικές από τις ίδιες ανέσεις.

```JavaScript
var obj = {
    // __proto__
    __proto__: theProtoObj,
    // Συντομογραφία του ‘handler: handler’
    handler,
    // Μέθοδοι
    toString() {
     // Κλήση super
     return "d " + super.toString();
    },
    // Δυναμική δημιουργία ονομάτων ιδιοτήτων
    [ 'prop_' + (() => 42)() ]: 42
};
```

### Πρότυπα συμβολοσειρών (template strings)
Τα πρότυπα συμβολοσειρών παρέχουν συντακτική ζάχαρη στην δημιουργία συμβολοσειρών. Αυτό είναι παρόμοιο με το χαρακτηριστικό παρεμβολής συμβολοσειρών στην Perl,  την Python και άλλα. Προαιρετικά, μια ετικέτα μπορεί να προστεθεί για να επιτρέψει την δημιουργία της συμβολοσειράς να προσαρμοστεί, αποφεύγοντας τις επιθέσεις ένεσης ή την δημιουργία υψηλότερου επιπέδου δομών δεδομένων από περιεχόμενο συμβολοσειρών.

```JavaScript
// Βασική δημιουργία συμβολοσειράς
`In JavaScript '\n' is a line-feed.`

// Συμβολοσειρά πολλαπλών γραμμών
`In JavaScript this is
 not legal.`

// Παρέμβαση συμβολοσειράς
var name = "Bob", time = "today";
`Hello ${name}, how are you ${time}?`

// Δόμηση ενός HTTP request prefix που χρησιμοποιείται για να ερμηνεύσει τις αντικαταστάσεις και την κατασκευή
GET`http://foo.org/bar?a=${a}&b=${b}
    Content-Type: application/json
    X-Credentials: ${credentials}
    { "foo": ${foo},
      "bar": ${bar}}`(myOnReadyStateChangeHandler);
```

### Αποδόμηση (Destructuring)
Η αποδόμηση επιτρέπει την δέσμευση χρησιμοποιώντας μοτίβα ταιριάσματος, με υποστήριξη για ταίριασμα πινάκων και αντικειμένων. Η αποδόμηση αποτυγχάνει ομαλά, παρόμοια με το πρότυπο αναζήτησης σε αντικείμενα `foo["bar"]`, παράγοντας την τιμή `undefined` όταν δεν βρίσκει κάτι.

```JavaScript
// Ταίριασμα λίστας
var [a, , b] = [1,2,3];

// Ταίριασμα αντικειμένων
var { op: a, lhs: { op: b }, rhs: c }
       = getASTNode()

// Συντόμευση ταιριάσματος αντικειμένου
// Δένει τα `op`, `lhs` και `rhs` στο πεδίο εφαρμογής
var {op, lhs, rhs} = getASTNode()

// Μπορεί να χρησιμοποιηθεί στην θέση παραμέτρων
function g({name: x}) {
  console.log(x);
}
g({name: 5})

// Ομαλή αποτυχία κατά την αποδόμηση
var [a] = [];
a === undefined;

// Ομαλή αποτυχία κατά την αποδόμηση με προεπιλεγμένες τιμές
var [a = 1] = [];
a === 1;
```

### Default + Rest + Spread
Callee-evaluated default parameter values.  Turn an array into consecutive arguments in a function call.  Bind trailing parameters to an array.  Rest replaces the need for `arguments` and addresses common cases more directly.

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
Block-scoped binding constructs.  `let` is the new `var`.  `const` is single-assignment.  Static restrictions prevent use before assignment.


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
Iterator objects enable custom iteration like CLR IEnumerable or Java Iterable.  Generalize `for..in` to custom iterator-based iteration with `for..of`.  Don’t require realizing an array, enabling lazy design patterns like LINQ.

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
Generators simplify iterator-authoring using `function*` and `yield`.  A function declared as function* returns a Generator instance.  Generators are subtypes of iterators which include additional  `next` and `throw`.  These enable values to flow back into the generator, so `yield` is an expression form which returns a value (or throws).

Note: Can also be used to enable ‘await’-like async programming, see also ES7 `await` proposal.

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
    return Math.log(x);
}
```
```JavaScript
// app.js
import ln, {pi, e} from "lib/mathplusplus";
alert("2π = " + ln(e)*pi*2);
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

### Math + Number + String + Array + Object APIs
Many new library additions, including core Math libraries, Array conversion helpers, String helpers, and Object.assign for copying.

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
