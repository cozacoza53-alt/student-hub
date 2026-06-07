// Завдання 1. Ініціалізація
console.log('Скрипт успішно ініціалізовано та підключено');

// Завдання 2. Проектування структури даних
const userName = "Олександр";
const userId = 5309;
const birthYear = 2004;

let currentAge = 22;
let isOnline = true;
let accountBalance = 1250.50;

console.log(`Користувач ${userName} (ID: ${userId}) онлайн: ${isOnline}. Поточний баланс: ${accountBalance}.`);

// Завдання 3. Дослідження примітивних типів
const typeNumber = 100;
const typeString = "Hello JS";
const typeBoolean = false;
const typeNull = null;
const typeUndefined = undefined;
const typeBigInt = 9007199254740991n;
const typeSymbol = Symbol("id");

console.log("Типи даних:");
console.log(typeof typeNumber);    // number
console.log(typeof typeString);    // string
console.log(typeof typeBoolean);   // boolean
console.log(typeof typeNull);      // object
console.log(typeof typeUndefined); // undefined
console.log(typeof typeBigInt);    // bigint
console.log(typeof typeSymbol);    // symbol

// Завдання 4. Математичні аномалії
const invalidCalculation = "Рядок" * 10;
const infiniteValue = 42 / 0;

console.log(`NaN результат: ${invalidCalculation}`);
console.log(`Infinity результат: ${infiniteValue}`);

// Порівняння NaN саме з собою
console.log("Чи дорівнює NaN сам собі?:", invalidCalculation === invalidCalculation); 
// Коментар: Результат false. NaN — це єдине значення в JS, яке не дорівнює самому собі.

// Завдання 5. Перетворення типів
console.log("Неявне перетворення:");
console.log(10 + "20"); // "1020" (конкатенація)
console.log("50" - 10); // 40 (рядок став числом при відніманні)
console.log(true * 100); // 100 (true перетворилося на 1)

console.log("Явне перетворення:");
const convertedNumber = Number("1024");
console.log(typeof convertedNumber, convertedNumber);

const boolZero = Boolean(0);
const boolEmptyString = Boolean("");
console.log(`Boolean(0): ${boolZero}, Boolean(""): ${boolEmptyString}`);

// Завдання 6. Строга рівність
const numericValue = 42;
const stringValue = "42";

console.log("Нестроге порівняння (==): ", numericValue == stringValue); // true
console.log("Строге порівняння (===): ", numericValue === stringValue); // false

// Результати відрізняються, бо === враховує тип даних без неявного приведення (число !== рядок).