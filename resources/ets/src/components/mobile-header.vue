<template>
  <div class="m-header">
    <slot />
  </div>
</template>

<script>
export default {
  name: "Breadcrumb",
  computed: {
    breadcrumbData() {
      const breadcrumb = {}
      console.log(this.$route.query.tab)
      switch (this.$route.query.tab) {
        case "1":
          breadcrumb.name = "全部工单"
          break
        case "2":
          breadcrumb.name = "我发起的"
          break
        case "3":
          breadcrumb.name = "我部门发起的"
          break
        case "4":
          breadcrumb.name = "指派给我的"
          break
        case "5":
          breadcrumb.name = "指派给我部门的"
          break
      }
      return breadcrumb
    }
  }
}
</script>
<style lang="scss" scoped>
  .m-header {
    position: fixed;
    top:0;
    width: 100%;
    height: 48px;
    background: #fff;
    border-bottom:1px solid #e6e6e6;
    text-align: center;
    line-height: 48px;
    z-index: 10;
    i {
      font-size: 20px;
    }
    .m-header-menu {
      position: absolute;
      left:16px;
      top:12px;
    }
    .m-header-back {
      position: absolute;
      left:14px;
      top:12px;
    }
    .m-header-search {
      position: absolute;
      right:16px;
      top:14px;
    }
    .m-header-more {
      position: absolute;
      right:18px;
      top:14px;
    }
    .m-header-add {
      position: absolute;
      right:52px;
      top:14px;
    }
  }
</style>

