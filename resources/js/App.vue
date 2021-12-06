<template>
  <div class="container mt-4">
    <div class="text-center">
      <router-link to="/" class="nav-item nav-link fs-2">
        <strong> Campaign Manager</strong>
      </router-link>
    </div>
    <div class="row mt-4">
      <div class="col-md-12">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>
<script>
export default {};
</script>