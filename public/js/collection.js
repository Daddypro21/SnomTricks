const collections = document.querySelectorAll('.collection');

function createRemoveButton() {
    const removeButton = document.createElement('button');
    removeButton.innerText = 'Remove';
    removeButton.type = 'button';

    removeButton.addEventListener('click', (e) => {
        e.preventDefault();

        removeButton.parentElement.remove();
    });
    return removeButton;
}

for (let collection of collections) {
    const addButton = document.createElement('button');
    addButton.innerText = 'Add';
    addButton.type = 'button';

    let label = collection.dataset.label ?? 'Element';

    collection.querySelectorAll('.collection>div').forEach((element) => {
        element.appendChild(createRemoveButton());
    });

    collection.querySelectorAll('.collection>div>label').forEach((element) => {
        element.innerText = label;
    });

    addButton.addEventListener('click', (e) => {
        e.preventDefault();

        const divPrototype = document.createElement('div');

        let prototype = collection.dataset.prototype
            .replace(/__name__label__/g, label)
            .replace(/__name__/g, Date.now() + Math.floor(Math.random() * 100));

        divPrototype.innerHTML = prototype;

        divPrototype.appendChild(createRemoveButton());
        collection.insertBefore(divPrototype, addButton);

        console.log(prototype);
    });

    collection.appendChild(addButton);
}
