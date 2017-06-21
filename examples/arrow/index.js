console.log("Hi")

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
