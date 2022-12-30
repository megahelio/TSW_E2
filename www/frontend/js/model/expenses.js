class ExpensesModel extends Fronty.Model {

  constructor() {
    super('PostsModel'); //call super

    // model attributes
    this.posts = [];
  }

  setSelectedExpenses(expense) {
    this.set((self) => {
      self.selectedExpenses = expense;
    });
  }

  setExpenses(expenses) {
    this.set((self) => {
      self.expenses = expenses;
    });
  }
}
