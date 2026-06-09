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
const typeString = "Hello JS";
const typeNumber = 100;
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


// ==========================================================================
// КОД ПРАКТИЧНОЇ РОБОТИ: Динамічне керування структурою та подіями в DOM
// ==========================================================================

// Отримання посилань на елементи сторінки
const appContainer = document.getElementById('app-container');
const todoForm = document.getElementById('todo-form');
const itemInput = document.getElementById('item-input');
const itemList = document.querySelector('.item-list');
const clearAllBtn = document.getElementById('clear-all-btn');
const generateItemsBtn = document.getElementById('generate-items-btn');
const infoPanel = document.getElementById('info-panel');
const notification = document.getElementById('notification-container');

/**
 * Завдання 5: Функція оновлення геометричних параметрів контейнера
 */
function updateMetrics() {
    if (!appContainer || !infoPanel) return;
    
    // Зчитуємо реальні розміри картки в пікселях
    const width = appContainer.offsetWidth;
    const height = appContainer.offsetHeight;
    
    infoPanel.textContent = `Ширина: ${width}px | Висота: ${height}px`;
}

/**
 * Допоміжна funktion для плавного показу спливаючого сповіщення
 */
function showSystemNotification(text) {
    if (!notification) return;
    
    notification.textContent = text;
    notification.classList.add('show');
    
    // Автоматично ховаємо сповіщення через 2.5 секунди
    setTimeout(() => {
        notification.classList.remove('show');
    }, 2500);
}

/**
 * Фабрична функція для створення структури елемента списку <li>
 */
function createListItem(text) {
    const li = document.createElement('li');
    
    // Безпечне додавання тексту через textContent для захисту від XSS (Завдання 2.6)
    const textSpan = document.createElement('span');
    textSpan.textContent = text;
    li.appendChild(textSpan);

    // Створення блоку керування (кнопки "Виконано" та "Видалити")
    const actionsDiv = document.createElement('div');
    actionsDiv.className = 'item-actions';

    const completeBtn = document.createElement('button');
    completeBtn.type = 'button';
    completeBtn.textContent = 'Виконано';
    completeBtn.className = 'complete-btn';

    const deleteBtn = document.createElement('button');
    deleteBtn.type = 'button';
    deleteBtn.textContent = 'Видалити';
    deleteBtn.className = 'delete-btn';

    actionsDiv.appendChild(completeBtn);
    actionsDiv.appendChild(deleteBtn);
    li.appendChild(actionsDiv);

    // Інтерактивні слухачі подій для динамічного класу is-active (Завдання 1)
    li.addEventListener('mouseenter', () => li.classList.add('is-active'));
    li.addEventListener('mouseleave', () => li.classList.remove('is-active'));

    return li;
}

// Завдання 2: Обробка форми та додавання нового елемента в кінець списку
if (todoForm) {
    todoForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Скасування перезавантаження сторінки (Завдання 2.3)

        const inputValue = itemInput.value.trim(); // Очищення від пробілів (Завдання 2.4)
        
        if (inputValue === "") {
            alert("Поле не може бути порожнім!");
            return;
        }

        // Генеруємо новий елемент та вставляємо сучасним методом append (Завдання 2.8)
        const li = createListItem(inputValue);
        itemList.append(li);

        // Очищення поля введення та повернення фокусу (Завдання 2.9)
        itemInput.value = "";
        itemInput.focus();

        // Оновлюємо метрики інтерфейсу та виводимо повідомлення
        updateMetrics();
        showSystemNotification("Елемент успішно додано!");
    });
}

// Завдання 3: Патерн делегування подій (один обробник на рівні батьківського <ul>)
if (itemList) {
    itemList.addEventListener('click', function(event) {
        const target = event.target; // Отримуємо первинний елемент кліку (Завдання 3.2)

        // Обробка кліку по кнопці "Видалити" (Завдання 3.3)
        if (target.classList.contains('delete-btn')) {
            const li = target.closest('li'); // Пошук найближчого батьківського тегу <li>
            if (li) {
                li.remove(); // Повне видалення елемента з дерева DOM
                updateMetrics(); // Перерахунок розмірів після видалення
                showSystemNotification("Елемент видалено.");
            }
        }

        // Обробка кліку по кнопці "Виконано" (Завдання 3.3)
        if (target.classList.contains('complete-btn')) {
            const li = target.closest('li');
            if (li) {
                li.classList.toggle('is-completed'); // Перемикання CSS-класу (Завдання 1)
            }
        }
    });
}

// Завдання 4.1: Швидке та продуктивне очищення списку через цикл firstChild
if (clearAllBtn) {
    clearAllBtn.addEventListener('click', function() {
        while (itemList && itemList.firstChild) {
            itemList.removeChild(itemList.firstChild);
        }
        updateMetrics();
        showSystemNotification("Список повністю очищено!");
    });
}

// Завдання 4.2: Пакетна генерація елементів через DocumentFragment (Захист від Layout Thrashing)
if (generateItemsBtn) {
    generateItemsBtn.addEventListener('click', function() {
        // Створюємо тимчасовий фрагмент у пам'яті (Завдання 4.2.3)
        const fragment = document.createDocumentFragment();

        for (let i = 1; i <= 20; i++) {
            const li = createListItem(`Згенерований елемент №${i}`);
            fragment.appendChild(li); // Додаємо елементи у фрагмент, не турбуючи дерево DOM
        }

        // Вставляємо всі 20 елементів за один єдиний виклик (Оптимізація продуктивності)
        itemList.appendChild(fragment);
        
        updateMetrics();
        showSystemNotification("Генерація 20 елементів завершена!");
    });
}

// Завдання 5.4: Слухач глобальної події зміни розміру вікна браузера
window.addEventListener('resize', updateMetrics);

// Розрахунок початкових розмірів відразу після завантаження дерева елементів сторінки
document.addEventListener('DOMContentLoaded', updateMetrics);