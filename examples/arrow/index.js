

console.log("Hi")

//named funciton and anonymous  function
function sayHello(name) {
  console.log(`Hello ${name}`);
}
sayHello("keshav");

const a=function (name){
  console.log("Hello ",name);
}
a("keshav");
// arrow funcitons are anonymous  function you can not name them insted you can store in variable

// reqular funciton
const some=function(x){ return x; }

const add=function(x){
    return x + 5;
}
console.log(some(5));
console.log(add(3));

// equivalent arrow function
const some1=(x)=>x;
const add5=(x)=>x+5;

console.log(some1(10));
console.log(add5(5));

// regular function
const fruits = ['Mango', 'Banana', 'Grapes'];
const fruitsCharLength=fruits.map(function(fruit){ return fruit.length});
console.log(fruitsCharLength);

//equivalent arrow function
const fruitsCharLength1=fruits.map((f)=>{ return f.length });
console.log(fruitsCharLength1);

//equivalent arrow function with implicit return and cury braces removed ie. signle line statement, use curybraces for multi line
const fruitsCharLength2=fruits.map(f => f.length );
console.log(fruitsCharLength2);

//no argument function
const fruitsCharLength3=fruits.map(()=> "same string" );
console.log(fruitsCharLength3);

const fruitsCharLength4=fruits.map(()=> "same string" );
console.log(fruitsCharLength4);
