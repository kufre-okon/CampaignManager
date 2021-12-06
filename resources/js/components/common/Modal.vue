<template>
  <div class="cmodal-backdrop">
    <div class="cmodal">
      <header class="cmodal-header">
        <div class="sub-header">
          <slot name="header"> This is the default title! </slot>
        </div>
        <button type="button" class="btn-close" @click="close">x</button>
      </header>

      <section class="cmodal-body">
        <slot name="body"> This is the default body! </slot>
      </section>

      <footer class="cmodal-footer">
        <slot name="footer"></slot>
        <div class="text-end">
          <button type="button" class="btn btn-secondary" @click="close">
            Close
          </button>
        </div>
      </footer>
    </div>
  </div>
</template>
<style scoped>
.cmodal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 100vw;
  z-index: 1050;
  background-color: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: center;
  transition: all 2s;
}

.cmodal {
  background: #ffffff;
  box-shadow: 2px 2px 20px 1px;
  overflow-x: auto;
  display: flex;
  flex-direction: column;
  max-width: 700px;
  min-width: 300px;
  border-radius: 10px;
}

.cmodal-header,
.cmodal-footer {
  padding: 15px;
  display: flex;
}

.cmodal-header {
  position: relative;
  border-bottom: 1px solid #eeeeee;
  color: #286dee;
  justify-content: space-between;
}

.sub-header {
  padding-right: 30px;
}
.cmodal-footer {
  border-top: 1px solid #eeeeee;
  flex-direction: column;
  justify-content: flex-end;
}

.cmodal-body {
  position: relative;
  padding: 20px 10px;
  max-height: calc(100vh - 150px);
  overflow-y: auto;
}

.btn-close {
  position: absolute;
  top: 0;
  right: 0;
  border: none;
  font-size: 20px;
  padding: 10px;
  cursor: pointer;
  font-weight: bold;
  color: #b82419;
  background: transparent;
}
</style>

<script>
export default {
  name: "Modal",
  methods: {
    close() {
      this.$emit("close");
    },
  },
};
</script>