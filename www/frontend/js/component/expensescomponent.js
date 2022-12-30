class ExpensesComponent extends Fronty.ModelComponent {
    constructor(expensesModel, userModel, router) {
        super(Handlebars.templates.expensestable, expensesModel, null, null);


        this.expensesModel = expensesModel;
        this.userModel = userModel;
        this.addModel('user', userModel);
        this.router = router;

        this.expensesService = new ExpensesService();

    }

    onStart() {
        this.updateExpenses();
    }

    updateExpenses() {
        this.expensesService.findAllExpenses().then((data) => {

            this.expensesModel.setExpenses(
                // create a Fronty.Model for each item retrieved from the backend
                data.map(
                    (item) => new this.expensesModel(item.id, item.name, item.type, item.amount, item.date, item.file, item.author_id)
                ));
        });
    }

    // Override
    createChildModelComponent(className, element, id, modelItem) {
        return new ExpenseRowComponent(modelItem, this.userModel, this.router, this);
    }
}

class ExpenseRowComponent extends Fronty.ModelComponent {
    constructor(expenseModel, userModel, router, expensesComponent) {
        super(Handlebars.templates.expenserow, expenseModel, null, null);

        this.expensesComponent = expensesComponent;

        this.userModel = userModel;
        this.addModel('user', userModel); // a secondary model

        this.router = router;
    }

}