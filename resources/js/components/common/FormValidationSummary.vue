<template>
  <div class="col-md-12" v-if="validationErrors.length">
    <div class="alert alert-danger fade show">
      <ul>
        <li v-for="(err, index) in validationErrors" :key="index">
          {{ err }}
        </li>
      </ul>
    </div>
  </div>
</template>
<script>
export default {
  props: ["validationErrors"],
};
</script>