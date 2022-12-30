class ExpenseModel extends Fronty.Model {

  constructor(id, type, date, amount, description, file, owner) {
    super('PostModel'); //call super
    
    if (id) {
      this.id = id;
    }
    
    if (type) {
      this.type = type;
    }
    
    
    if (date) {
      this.date = date;
    }
    
    if (amount) {
      this.amount = amount;
    }
    
    if (description) {
      this.description = description;
    }

    if (file) {
      this.file = file;
    }

    if (owner) {
      this.owner = owner;
    }
  }

  setType(type) {
    this.set((self) => {
      self.type = type;
    });
  }

  setDate(date) {
    this.set((self) => {
      self.date = date;
    });
  }

  setAmount(title) {
    this.set((self) => {
      self.amount = amount;
    });
  }

  setDescription(description) {
    this.set((self) => {
      self.description = description;
    });
  }

  setFile(file) {
    this.set((self) => {
      self.file = file;
    });
  }

  setAuthor(owner) {
    this.set((self) => {
      self.owner = owner;
    });
  }
}
