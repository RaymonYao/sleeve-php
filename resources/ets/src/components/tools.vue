<template>
  <div>
    <el-dialog
      :class="['mobile-box drawer', animation]"
      v-if="$ismobile"
      :modal="false"
      :visible.sync="visible"
      append-to-body
      width="100%"
    >
      <div class="drawer-header">
        <i @click="handleClose" class="el-icon-arrow-left fl"></i>
        {{ titleText }}
      </div>
      <div class="drawer-body">
        <slot />
      </div>
    </el-dialog>
    <div v-else class="tools">
      <slot />
    </div>
  </div>
</template>
<script>
export default {
  props: {
    title: {
      type: String,
      default: ""
    },
    visible: false,
  },
  data() {
    return {
      animation: "mounted leftout",
      isShow: this.visible,
      titleText: this.title
    }
  },
  watch: {
    visible(n, o) {
      if (n) {
        this.animation = "leftin"
        document.querySelector("body").classList.add('fixed')
      } else {
        this.animation = "leftout"
        document.querySelector("body").classList.remove('fixed')
      }
    }
  },
  mounted() {
    if (this.$ismobile) {
      this.$nextTick(() => {
        const body = document.querySelector("body")
        if (body.append) {
          body.append(this.$el)
        } else {
          body.appendChild(this.$el)
        }
      })
    }
  },
  methods: {
    handleClose() {
      this.isShow = false
      this.$emit("update:visible", false)
    }
  }
}
</script>
