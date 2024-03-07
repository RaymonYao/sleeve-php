<template>
  <div :class="['topmenu',{show:visible}]">
     <ul class="clearfix">
       <li @click="changeMenu(index,item.tab)" :class="{ active:item.actvie }" v-for="(item,index) in menu" :key="item.id">{{ item.name }}</li>
     </ul>
    <div @click="closeMenu" class="masker"></div>
  </div>
</template>

<script>
  export default {
    props: {
      visible: false,
    },
    data() {
      return {
        menu: [
          { name: "全部工单",tab: "0",actvie:true},
          { name: "我发起的",tab: "1",actvie:false},
          { name: "我部门发起的",tab: "2",actvie:false},
          { name: "指派给我的",tab: "3",actvie:false},
          { name: "指派给我部门的",tab: "4",actvie:false}
        ],
      }
    },
    name: 'TopMenu',
    created() {
      let tabIndex = this.$route.query?.tab;
      if (tabIndex === undefined) tabIndex = '0';
      this.menu.forEach(i => {
        if (i.tab === tabIndex) {
          i.actvie = true
        } else {
          i.actvie = false
        }
      })
    },
    methods: {
      changeMenu(index,number) {
        console.log(number)
        this.$router.push({path: "/",query: {tab: number}})
        this.menu.forEach(i => {
          i.actvie = false
        })
        this.menu[index].actvie = true
        this.closeMenu()
      },
      closeMenu() {
        this.$emit("update:visible",false)
      }
    }
  }
</script>
<style lang="scss" scoped>
  .topmenu {
    width: 100%;
    height: 48px;
    line-height: 48px;
    background: #fff;
    font-size: 14px;
    border-bottom: 1px solid #e6e6e6;
    ul {
      position: relative;
      z-index: 2;
      display: table;
      width: 100%;
      background: #fff;
      li {
        width: 15%;
        min-width: 120px;
        text-align: center;
        float: left;
        position: relative;
        cursor: pointer;
        &:after {
          position: absolute;
          content: '';
          bottom: 0;
          left: 10%;
          right: 10%;
          opacity: 0;
          min-width: 100px;
          -webkit-transform: scale(0, 1);
          transform: scale(0, 1);
          -webkit-transform-origin: center center;
          transform-origin: center center;
          border-bottom: 2px solid #409eff;
          -webkit-transition: opacity 0.3s, -webkit-transform 0.3s;
          transition: opacity 0.3s, -webkit-transform 0.3s;
          transition: transform 0.3s, opacity 0.3s, -webkit-transform 0.3s;
        }
        &.active {
          color:#409eff;
        }
        &.active:after {
          opacity: 1;
          -webkit-transform: scale(1, 1);
          transform: scale(1, 1);
        }
      }
    }
  }
  @media (max-width: 767px) {
    .topmenu {
      display: none;
      position: fixed;
      height:auto;
      top:48px;
      left:0;
      right: 0;
      z-index: 999;
      background: #fff;
      ul{
        li{
          width: 100%;
          text-align: left;
          padding: 0 16px;
          border-bottom: 1px solid #e6e6e6;
          &:after {
            content: none;
          }
        }
      }
    }
    .topmenu.show{
      display: block;
    }
    .masker{
      position: fixed;
      z-index: 1;
      top:48px;
      left:0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,.3);
    }
  }
</style>
