//components
import ArticlesIndex from "../Pages/Blog/ArticlesIndex";
import ArticleShow from "../Pages/Blog/ArticleShow";
import NotFound from "../Pages/NotFound";
import CategoryIndex from "../Pages/Blog/CategoryIndex";
import TagIndex from "../Pages/Blog/TagIndex";



const routes = [
    {
        path: "/",
        component: ArticlesIndex,
        name: "posts.index"
    },
    {
        path: "/categories/:category",
        component: CategoryIndex,
        name: "categories.index"
    },
    {
        path: "/tags/:tag",
        component: TagIndex,
        name: "tags.index"
    },
    {
        path: "/:category/:slug",
        component: ArticleShow,
        name: "posts.show"
    },
    {
        path: "*",
        component: NotFound,
        name: "not found"
    }
];

export default routes;