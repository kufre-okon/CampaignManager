<template>
  <div>
    <div v-if="bannerFiles.length">
      <div class="d-flex align-items-center justify-content-even flex-wrap">
        <div
          v-for="(banner, index) in bannerFiles"
          :key="banner.id || index"
          class="mb-3 mt-1 mr-1 ml-1 banner-wrapper"
        >
          <img
            class="img-thumbnail"
            :alt="'banner image' + (banner.id || index)"
            :src="banner.image_url"
            style="height: 100%"
          />
          <span
            class="btn btn-danger btn-sm banner-button"
            @click="remove(banner.id || index)"
            v-show="showRemoveButton"
            >Remove</span
          >
        </div>
      </div>
    </div>
    <div class="jumbotron" v-else-if="showEmptyListMessage">
      <div class="text-info fs-2"><em>No creative banner found....</em></div>
    </div>
  </div>
</template>
<style scoped>
.banner-wrapper {
  width: 200px;
  height: 200px;
  position: relative;
}
.banner-wrapper:hover .banner-button {
  display: inline;
}
.banner-button {
  display: none;
  position: absolute;
  top: 50%;
  left: 50%;
  margin: -35px 0 0 -35px;
  -webkit-transition: all 0.3s ease-out; /* Chrome 1-25, Safari 3.2+ */
  -moz-transition: all 0.3s ease-out; /* Firefox 4-15 */
  -o-transition: all 0.3s ease-out; /* Opera 10.50–12.00 */
  transition: all 0.3s ease-out; /* Chrome 26, Firefox 16+, IE 10+, Opera     12.50+ */
}
</style>
<script>
export default {
  props: ["showEmptyListMessage","showRemoveButton", "bannerFiles"],
  methods: {
    remove(id) {
      this.$emit("removeBanner", id);
    },
  },
};
</script>