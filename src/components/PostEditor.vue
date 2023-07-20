<template>
  <div class="post-editor">
    <div class="header">
      <button :disabled="!changed" @click="savePost">SAVE</button>
      <button @click="$emit('cancel')">CANCEL</button>

      <span><b>EDITING:</b> {{postMetaData.title}} ({{post}})</span>
    </div>
    <div class="metadata">
      <div class="md-item" v-for="(value,key) in postMetaData" :key="key">
        <label> {{key}}</label>

        <input v-if="typeof(value)== 'string'" type="text" :value="value" @input="updateMetadataField(key,$event)"/>
        <input v-if="typeof(value)== 'number'" type="number" :value="value" @input="updateMetadataField(key,$event)"/>
        <input v-if="typeof(value)=='boolean'" type="checkbox" :checked="value" @change="updateMetadataField(key,$event)"/>
        <StringList v-if="typeof(value)=='object'" :list="value" :field="key" @delete="deleteStringFromList" @update="updateStringInList" @insert="insertStringInList"/>
      </div>
    </div>
    <div class="md-content">
      <textarea @scroll="scrollPreview"  v-model="postContent" @input="changed=true"></textarea>
    </div>
    <div class="md-preview" v-html="htmlContent" @wheel.stop.prevent>
    </div>
  </div>
</template>

<script>
import {api_url} from "@/main"
import {Md} from "@/main"
import StringList from "@/components/StringList";
export default {
  name: "PostEditor",
  props:['post'],
  components:{
  StringList,
  },
  data(){
    return{
      changed:false,
      postMetaData:{},
      postContent:"",
      htmlContent:"",
    }
  },
  emits:['cancel','saved'],
  methods:{
    getPost(post){
      this.changed=false
      this.axios.get(api_url+"?action=getPost",{params:{'post':post}}).then((response) => {
        Object.keys(response.data).forEach((key) => {if(key==="content") this.postContent=response.data[key]; else {if(key!=="mDataEndPos") this.postMetaData[key]=response.data[key];}})

      })
          .catch(function (error) {
            console.log(error);
          })
          .finally(() => {
           // console.log(this.postContent)
          });
    },
    savePost(){
      this.axios({method:'post', url:api_url+"?action=savePost&post="+this.post,
          data:{metaData:this.postMetaData, content:this.postContent},
            headers: {
              'Content-Type': 'application/json'
            },},
      )
          .then((response) => {
          console.log(response.data)
          this.$emit('saved')
          this.$emit('cancel')

        })
          .catch(function (error) {
              console.log(error);
            })
          .finally(() => {
              // console.log(this.postContent)
            });
    },
    scrollPreview(event){
      let scroll = event.target.scrollTop / (event.target.scrollHeight-event.target.clientHeight)
      let prev = document.querySelector(".md-preview")
      prev.scrollTo(0, scroll*(prev.scrollHeight-prev.clientHeight))

    },
    deleteStringFromList(field,value)
    {
      this.postMetaData[field].splice(value,1)
      this.changed=true
    },
    updateStringInList(field,id,value)
    {
      this.postMetaData[field][id]=value
      this.changed=true
    },
    insertStringInList(field)
    {
      this.postMetaData[field].push("")
      this.changed=true
    },
    updateMetadataField(field,event)
    {
      if(event.target.type==='checkbox'){
        this.postMetaData[field]=event.target.checked
      } else this.postMetaData[field]=event.target.value
      this.changed=true
    },

  },
  watch:{
    postContent(data){
      this.htmlContent = Md.render(data)
      return data
    },
  },
  mounted() {
    this.getPost(this.post)

  }
}
</script>

<style scoped>
.post-editor *{
  border-collapse: collapse;
  box-sizing: border-box;
}
.post-editor{
  background: #eee;
  display: flex;
  height: 90vh;
  top: 5vh;
  flex-flow: row wrap;
  position: fixed;
  width: 90vw;
  right: 0;
  transition: all 1s ease;
  box-shadow: 0px 0px 40px #000;
  border-radius: 15px 0 0 15px;
}
.header{
  padding: 5px;
  display: flex;
  background: #a3bdcc;
  gap: 5px;
  flex-basis: 100%;
  height: 30px;
}
.metadata
{
  flex-basis: 20%;
  height: calc(100% - 30px);
  overflow-y: scroll;
  padding: 10px;
}
.md-item{
  border-top:1px solid #bbbbbb;
  margin-bottom: 8px;
}

.metadata input:not([type=checkbox]){
width: 100%;
}

.metadata label{
  font-size: 8pt;
}
.md-content
{
  height: calc(100% - 30px);
  flex-basis: 40%;

}
.md-content textarea{
  width: 100%;
  height: 100%;
  padding: 10px;
}

.md-preview
{
  flex-basis: 40%;
  overflow-y: scroll;
  height: calc(100% - 30px);
  padding: 10px;
}

</style>