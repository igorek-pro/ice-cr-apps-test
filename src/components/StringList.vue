<template>
<div :title="field">
  <div class="string-list-item" v-for="(item,id) in list"  :key="id">
    <input @change="$emit('update',field,id,$event.target.value)" type="text" :value="item">
    <button @click="$emit('delete',field,id)">X</button>
  </div>

  <button @click="$emit('insert',field)">+</button>
</div>
</template>

<script>
export default {
  name: "StringList",
  props:['list','field'],
  emits:['delete','update','insert'],
}
</script>

<style scoped>
.string-list-item{
  display: flex;
  width: 100%;
}
.string-list-item input{
  flex-grow: 1;
}
</style>