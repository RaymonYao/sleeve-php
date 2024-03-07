<template>
  <div class="breadcrumb fl">
      <span class="el-breadcrumb__separator">></span>
    {{ breadcrumbData.name }}
  </div>
</template>

<script>
export default {
  name: "Breadcrumb",
  computed: {
    breadcrumbData() {
      const breadcrumb = {}
      if(this.$route.name === "Detail"){
        breadcrumb.name = "工单详情"
      }
      if(this.$route.name === "List") {
        switch (this.$route.query.tab) {
          case undefined:
            breadcrumb.name = "全部工单"
            break
          case "0":
            breadcrumb.name = "全部工单"
            break
          case "1":
            breadcrumb.name = "我发起的"
            break
          case "2":
            breadcrumb.name = "我部门发起的"
            break
          case "3":
            breadcrumb.name = "指派给我的"
            break
          case "4":
            breadcrumb.name = "指派给我部门的"
            break
        }
      }
      return breadcrumb
    }
  }
}
</script>
<style lang="scss" scoped>
  .breadcrumb {
    font-size:13px;
    line-height: 32px;
  }
</style>
