<template>
  <div class = "filterBlock">
    <input type="text" placeholder="Filter by Title, Url, Content" v-model="textFilter"/>
    <PostsFilter  :optionSet = toolFilterSet v-model="Filters.tool"  Default = "Select Tool to filter" Field="tool"/>
    <PostsFilter  :optionSet = statusFilterSet v-model="Filters.status"  Default = "Select Status to filter" Field="status" />
    <PostsFilter  :optionSet = authorFilterSet v-model="Filters.author"  Default = "Select Author to filter" Field="author" />
    <button @click="clearFilters">Clear</button>

  </div>
  <PostsTable :Posts = "filteredPosts" class="postsTable" @editPost="editPost"/>
<transition appear name="slide">
  <PostEditor class="post-editor" v-if="editor" :post="editorFile" @cancel="cancelEditPost" @saved="getPosts"/>
</transition>
</template>

<script>

import PostsTable from './components/PostsTable.vue'
import PostsFilter from "./components/PostsFilter.vue";
import PostEditor from "@/components/PostEditor";
import {api_url} from "@/main";

export default {
  name: 'App',
  components: {
    PostEditor,
    PostsFilter,
    PostsTable
  },
  data()
  { return {
          posts:[],


          Filters:{
            'tool':"-undef-",
            'status':"-undef-",
            'author':"-undef-",
          },
          textFilter:"",
          backendSearchResult:[],
          editor:false,
          editorFile:"",


    }
  },
  methods:{
    getPosts(){
      this.axios.get(api_url).then((response) => {

        this.posts = response.data
      })
          .catch(function (error) {
            console.log(error)
          })
          .finally(() => {

          });
    },

    clearFilters(){
      this.Filters['tool'] = "-undef-"
      this.Filters['status'] = "-undef-"
      this.Filters['author'] = "-undef-"
      this.textFilter = ""
      this.backendSearchResult.length = 0

    },
    editPost(post){
      this.editor=true
      this.editorFile = post
    },
    cancelEditPost(){
      this.editor=false
      this.editorFile = ""
    },
    searchOnContent(query){
      this.axios.get(api_url+"?action=searchPosts",{params:{'search':query}}).then((response) => {

        this.backendSearchResult.length = 0
        this.backendSearchResult.push(...response.data?.found)})
          .catch(function (error) {
            console.log(error);
          })
          .finally(() => {
            // console.log(this.postContent)
          });
    }
  },
  computed:{
    toolFilterSet(){
      let filterSet = new Set()
      this.posts.forEach(item => filterSet.add(item['tool']??""))
      return filterSet
    },
    statusFilterSet(){
      let filterSet = new Set()
      this.posts.forEach(item => filterSet.add(item['status']??""))
      return filterSet
    },
    authorFilterSet(){
      let filterSet = new Set()
      this.posts.forEach(item => filterSet.add(item['author']??""))
      return filterSet
    },

    filteredPosts(){
      console.log(this.Filters)
      return this.posts


          .filter( item=>{                //SOFT FILTER by url & title
            return (!(
                this.textFilter.length>1 &&
                item.path.toLowerCase().indexOf(this.textFilter.toLowerCase())===-1 &&
                item.title.toLowerCase().indexOf(this.textFilter.toLowerCase())===-1)
            ||
                                          //SOFT FILTER by content from backend
                (this.textFilter.length>3 && this.backendSearchResult.includes(item.path)))})



          .filter( item=>{                //strong filter by tool,status,author selectors
        for (let key in this.Filters){
               if(this.Filters[key]!=="-undef-" &&
                 ((item[key]??"")!==this.Filters[key])

             ) return false
        }
        return true
      })
    }
  },
  watch:{
    textFilter(data){
      if (data.length>3){
        this.searchOnContent(data)
      } else this.backendSearchResult.length = 0
      return data
    }
  },
  mounted() {
    this.getPosts()

  }
}
</script>

<style>
body{
  margin: 0;
}
#app {
  font-family: Roboto, Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: left;
  color: #2c3e50;
  margin-top: 60px;
}
.filterBlock, .postsTable{
  margin: 10px;
}


.slide-enter-active {
  transition: all 2s ease;
  transform: translateX(100vw);
}
.slide-leave-active {
  transition: all 2s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-enter, .slide-leave-to
   {
  transform: translateX(200px);
  opacity: 0;
}

</style>
