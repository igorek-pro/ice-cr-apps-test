<template>

<table class="post-table"  >

    <tr>
      <th class="postId">
        #
      </th>
      <th @click="switchSort" data-field='url' class="postUrl">
        URL
      </th>
      <th @click="switchSort" data-field='title'  class="postTitle">
        TITLE
      </th>
      <th>

      </th>
      <th @click="switchSort" data-field='status'>
        STATUS
      </th>
      <th @click="switchSort" data-field='author'>
        AUTHOR
      </th>
      <th @click="switchSort" data-field='category'>
        CATEGORY
      </th>
      <th @click="switchSort" data-field='tool'>
        TOOL
      </th>
      <th @click="switchSort" data-field='views'>
        VIEWS
      </th>
      <th @click="switchSort" data-field='published_on'>
        PUBLISHED ON
      </th>
      <th @click="switchSort" data-field='modified_on'>
        MODIFIED ON
      </th>
      <th>

      </th>
    </tr>


    <tr v-for="(value, key) in sortedFilteredPosts" :key="value.views">
      <td class="postId">
        {{ key+1}}
      </td>
      <td class="postUrl">
        <a :href="'//localhost:8000/api?post='+value.path" target="_blank">{{ value.path }}</a>
      </td>
      <td class="postTitle">
        {{ value.title }}
      </td>
      <td class="postEdit">
        <button @click="$emit('editPost',value.path)">EDIT</button>
      </td>
      <td class="postStatus">
        {{ value.status }}
      </td>
      <td class="postAuthor">
        {{ value.author }}
      </td>
      <td class="postCategory">
        {{ value.category }}
      </td>
      <td class="postTool">
        <a href="#">{{ value.tool }}</a>
      </td>
      <td class="postViews">
        {{ value.views }}
      </td>
      <td class="postPublishDate" :title="value.published_on">
        {{ String(value.published_on).slice(0,10)}}
      </td>
      <td class="postModifiedDate" :title="value.modified_on">
        {{ String(value.modified_on).slice(0,10) }}
      </td>
      <td class="postAction">
        <button>UNPUBLISH</button>
      </td>
    </tr>

</table>
</template>

<script>
export default {
  name: "PostsTable",
  data(){
    return {
      sortField : "views",
      sortDir : 1,
      toolFilter:"",
      publishedFilter:"",
      authorFilter:"",

  }

  },
  emits:['editPost'],
  props: {
    Posts: {
      type: Array
    },
  },
  computed:{
    sortedFilteredPosts(){

      let filtered = this.Posts.filter(() => true )
      let sortBy = (d1, d2) => { return (d1[this.sortField] > (d2[this.sortField]??"")) ? this.sortDir : -this.sortDir;};
      return filtered.sort(sortBy)

    },
  },
  methods: {
    switchSort(event){
      
      let field = event?.target?.getAttribute('data-field')
      console.log(field)
       if(field === this.sortField) {this.sortDir*= -1} else {
        this.sortField = field;
        this.sortDir = 1;

      }
       [...event?.target?.parentElement.children].map(val => val.setAttribute('class',""))
      event?.target.setAttribute('class', (this.sortDir!==1?"sortUp":"sortDown"))

    }
  },

}
</script>

<style scoped>
.post-table{
  width:calc(100% - 20px);
  border-spacing: 0;
  border-collapse: collapse;
}
.post-table th:after,.post-table th:before
 {
   content: '\A0';
 }
.post-table th.sortUp:after
{
  content: '\2191';
}

.post-table th.sortDown:after
{
  content: '\2193';

}
.post-table th.sortDown,.post-table th.sortUp{
  background-color: #2087ba;
}

.post-table td, .post-table th
{
  border: 1px solid #a3bdcc;
  border-spacing: 0;
  white-space: nowrap;
}

.post-table th{
  text-align: center;
}
.post-table tr.published{
  background-color: #e7f2ff;

}
.post-table tr{

}
.post-table .postUrl a {
  text-overflow:ellipsis;
  overflow: hidden;
  max-width:170px;
  display: inline-block;
}
.post-table .postId{
  width:max-content;
}
.post-table th{
  cursor: pointer;
  background-color: #3399cc;
  color: #e7f2ff;

}

</style>