<template>
  <select @change="$emit('update:modelValue', $event.target.value)" :value="modelValue"  title="Default">
    <option selected value="-undef-" >{{Default}}</option>
    <option v-for="value in optionSet" :value="value" :key="value">{{value==""?"&lt;empty&gt;":value }}</option>
  </select>
</template>

<script>
export default {
  name: "PostsFilter",
  props:['optionSet','Default','Field','modelValue'],
  emits:['update:modelValue'],
  data(){
    return{

    }
  },
}
</script>

<style scoped>

</style>