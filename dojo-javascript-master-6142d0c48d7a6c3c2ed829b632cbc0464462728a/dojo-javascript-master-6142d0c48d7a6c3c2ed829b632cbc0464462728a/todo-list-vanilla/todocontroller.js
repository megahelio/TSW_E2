class TodoController {
    constructor() {
        this.todos = [{
                name: 'lunch',
                description: '13.00h, A Palleira Restaurant'
            },
            {
                name: 'dojo',
                description: '17.00h. Create a todo list!'
            },

        ];
    }
    init() {
        this.updateTodos();
        this.createAddButtonListener();
    }

    updateTodos() { // <---- full render!!

        var todosListElement = document.getElementById('todos');
        todosListElement.innerHTML = ''; // <--- DOM destroyed!

        this.todos.forEach((todo, index) => {
            var todoItemElement = document.createElement('li');
            todoItemElement.id = 'todo-item-' + index;

            var todoItemLabelElement = document.createElement('span');
            todoItemLabelElement.classList.add('name');
            todoItemLabelElement.appendChild(document.createTextNode(todo.name));
            var todoItemDescriptionElement = document.createElement('div');

            todoItemDescriptionElement.appendChild(document.createTextNode(todo.description));
            todoItemDescriptionElement.classList.add('description');
            todoItemDescriptionElement.classList.add('hidden');


            todoItemElement.classList.add('todo-item');

            if (todo.done) {
                todoItemElement.classList.add('done');
            }

            var todoItemDoneButton = document.createElement('input');

            todoItemDoneButton.type = 'button';
            todoItemDoneButton.value = todo.done ? 'Reopen' : 'Done';
            todoItemDoneButton.id = 'done-item-' + index;

            todoItemElement.appendChild(todoItemLabelElement);
            todoItemElement.appendChild(todoItemDoneButton);
            todoItemElement.appendChild(todoItemDescriptionElement);

            todosListElement.appendChild(todoItemElement);

        });

        this.createTodoListeners();
    }

    toggleDoneItem(todo) {
        return () => {
            console.log('closing item');
            todo.done = !todo.done;
            this.updateTodos(); // <---- force re-render
        }
    }

    createAddButtonListener() {
        document.getElementById('add-button').addEventListener('click', () => {
            var newTodoName = document.getElementById('todo-name').value;
            var newTodoDescription = document.getElementById('todo-description').value;

            this.todos.push({
                name: newTodoName,
                description: newTodoDescription
            });
            this.updateTodos(); // <--- force re-render
        });
    }

    createTodoListeners() {
        this.todos.forEach((todo, index) => {
            document.getElementById('done-item-' + index).addEventListener(
              'click', this.toggleDoneItem(todo));
            
            document.getElementById('todo-item-' + index)
              .getElementsByClassName('name')[0].addEventListener(
              'click', (event) => {

                event.target.parentNode.getElementsByClassName('description')[0]
                  .classList.toggle('hidden');

            })
        });
    }

}
var todocontroller = new TodoController();