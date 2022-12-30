class ExpenseViewComponent extends Fronty.ModelComponent {
    constructor(postsModel, userModel, router) {
        super(Handlebars.templates.postview, postsModel);

        this.postsModel = postsModel; // posts
        this.userModel = userModel; // global
        this.addModel('user', userModel);
        this.router = router;

        this.postsService = new PostsService();

    }

    onStart() {
        var selectedId = this.router.getRouteQueryParam('id');
        this.loadPost(selectedId);
    }

    loadPost(postId) {
        if (postId != null) {
            this.postsService.findPost(postId)
                .then((post) => {
                    this.postsModel.setSelectedPost(post);
                });
        }
    }
}