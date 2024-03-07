<template>
    <div class="list">
        <mobile-header v-if="$ismobile">
            <i @click="showMobilemenu = !showMobilemenu" class="icon-menu m-header-menu"></i>
            <div class="title">{{ titleData.name }}</div>
            <i @click="addTicket" class="el-icon-plus m-header-add"></i>
            <i @click="showFilterpopup" class="el-icon-search m-header-search"></i>
        </mobile-header>
        <top-menu :visible.sync="showMobilemenu"/>
        <div v-show="!$ismobile" class="row bg-white">
            <div class="row-inline clearfix">
                <bread-crumb/>
                <el-button @click="addTicket" class="newworkorder fr" type="success" size="small"><i
                    class="el-icon-plus"></i>发起工单
                </el-button>
            </div>
            <Tools
                :visible.sync="filterPopup"
                title="搜索"
            >
                <el-form :inline="true" :model="form" class="demo-form-inline">
                    <el-form-item label="">
                        <el-input v-model="form.title" placeholder="工单标题"></el-input>
                    </el-form-item>
                    <el-form-item label="">
                        <el-select :class="{isclear:!form.status}" v-model="form.status" placeholder="工单状态">
                            <el-option value="0" label="工单状态"/>
                            <el-option value="1" label="待处理"></el-option>
                            <el-option value="2" label="处理中"></el-option>
                            <el-option value="3" label="已完成"></el-option>
                            <el-option value="4" label="已取消"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="">
                        <el-select :class="{isclear:!form.created_by}" class="removeready" v-model="form.created_by"
                                   filterable placeholder="发起人">
                            <el-option value="0" label="发起人"></el-option>
                            <el-option
                                v-for="item in staff"
                                :key="item.uid"
                                :label="item.username"
                                :value="item.uid">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="">
                        <el-select :class="{isclear:!form.created_by_dept}" class="removeready"
                                   v-model="form.created_by_dept" filterable placeholder="发起部门">
                            <el-option :value="0" label="发起部门"/>
                            <el-option
                                v-for="item in department"
                                :key="item.dept_id"
                                :label="item.dept_name"
                                :value="item.dept_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="">
                        <el-select :class="{isclear:!form.final_handler}" class="removeready"
                                   v-model="form.final_handler" filterable placeholder="最后处理人">
                            <el-option :value="0" label="最后处理人"></el-option>
                            <el-option
                                v-for="item in staff"
                                :key="item.uid"
                                :label="item.username"
                                :value="item.uid">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="">
                        <el-select :class="{isclear:!form.is_overdue}" v-model="form.is_overdue" placeholder="逾期">
                            <el-option value="0" label="逾期"/>
                            <el-option label="是" value="1"></el-option>
                            <el-option label="否" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="">
                        <el-date-picker
                            v-model="form.start_date"
                            class="addready"
                            type="daterange"
                            range-separator="~"
                            start-placeholder="发起时间"
                            value-format="yyyy-MM-dd"
                            end-placeholder="">
                        </el-date-picker>
                    </el-form-item>
                    <el-form-item label="">
                        <el-date-picker
                            v-model="form.end_date"
                            class="addready"
                            type="daterange"
                            range-separator="~"
                            start-placeholder="结束时间"
                            value-format="yyyy-MM-dd"
                            end-placeholder="">
                        </el-date-picker>
                    </el-form-item>
                    <el-form-item>
                        <el-button @click="search" type="primary" size="small"><i class="el-icon-search"></i>查询
                        </el-button>
                    </el-form-item>
                </el-form>
            </Tools>
        </div>
        <div class="row bg-gray p-0 autoheight">
            <div class="table-box">
                <el-table
                    v-if="!$ismobile"
                    :data="tableData.data"
                    class="table-style-1"
                    header-row-class-name="header-gray"
                >
                    <div class="nodata" slot="empty"></div>
                    <el-table-column
                        prop="ticket_id"
                        label="工单编号"
                        min-width="100">
                    </el-table-column>
                    <el-table-column
                        prop="title"
                        label="工单标题"
                        min-width="200">
                        <template slot-scope="scope">
                            <div>
                                <router-link :to="{name: 'Detail', query: {id: scope.row.ticket_id }}">
                                    {{ scope.row.title }}
                                </router-link>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column
                        prop="status"
                        label="工单状态"
                        min-width="80"
                    >
                        <template slot-scope="scope">
                            <div>
                                <el-tag v-if="scope.row.status === 1" size="small" type="">待处理</el-tag>
                                <el-tooltip v-if="scope.row.status === 2" class="item" effect="dark"
                                            :content="'承接人: ' + (scope.row.assign_to_user ? scope.row.assign_to_user.username : '-')"
                                            placement="right">
                                    <el-tag size="small" type="">处理中</el-tag>
                                </el-tooltip>
                                <el-tooltip v-if="scope.row.status === 3 || scope.row.status === 4" class="item"
                                            effect="dark" :content="'结束时间: ' + scope.row.finish_at"
                                            placement="right">
                                    <el-tag v-if="scope.row.status === 3" size="small" type="success">已完成</el-tag>
                                    <el-tag v-if="scope.row.status === 4" size="small" type="danger">已取消</el-tag>
                                </el-tooltip>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column
                        prop="overdue_status"
                        label="逾期标记"
                        min-width="80"
                    >
                        <template slot-scope="scope">
                            <el-tag v-if="scope.row.overdue_status === 2" size="small" type="success">未逾期</el-tag>
                            <el-tag v-if="scope.row.overdue_status === 3" size="small" type="warning">将逾期</el-tag>
                            <el-tag v-if="scope.row.overdue_status === 1" size="small" type="danger">已逾期</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column
                        prop="created_user.username"
                        label="发起人"
                        min-width="80"
                    >
                    </el-table-column>
                    <el-table-column
                        prop="final_handler_user.username"
                        label="最后处理人"
                        min-width="80"
                    >
                    </el-table-column>
                    <el-table-column
                        prop="created_at"
                        label="发起时间"
                        min-width="180"
                    >
                    </el-table-column>
                    <el-table-column
                        prop="expect_finish_at"
                        label="期望结束时间"
                        min-width="180"
                    >
                    </el-table-column>
                    <el-table-column
                        label="操作"
                        min-width="180"
                        fixed="right"
                    >
                        <template slot-scope="scope">
                            <div>
                                <el-button v-if="scope.row.permission.showEdit" @click="editTicket(scope.row.ticket_id)"
                                           size="mini"><i class="el-icon-edit"></i>编辑
                                </el-button>
                                <el-button v-if="scope.row.permission.showAssign"
                                           @click="showPopupassign(scope.row.ticket_id)" size="mini"><i
                                    class="el-icon-thumb"></i>指派
                                </el-button>
                            </div>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div v-if="$ismobile" class="table-box-mobile">
                <div v-if="tableData.data && tableData.data.length < 1">
                    <div class="nodata"></div>
                </div>
                <div v-for="item in tableData.data">
                    <router-link :to="{name: 'Detail', query: {id: item.ticket_id }}">
                        <ul class="card">
                            <li class="title">{{ item.title }}</li>
                            <li>
                                <el-tag v-if="item.status === 1" size="small" type="">待处理</el-tag>
                                <el-tag v-if="item.status === 2" size="small" type="">处理中</el-tag>
                                <el-tag v-if="item.status === 3" size="small" type="success">已完成</el-tag>
                                <el-tag v-if="item.status === 4" size="small" type="danger">已取消</el-tag>

                                <el-tag v-if="item.overdue_status === 2" size="small" type="success">未逾期</el-tag>
                                <el-tag v-if="item.overdue_status === 3" size="small" type="warning">将逾期</el-tag>
                                <el-tag v-if="item.overdue_status === 1" size="small" type="danger">已逾期</el-tag>
                            </li>
                            <li>
                                <span v-if="item.created_user">发起人: {{ item.created_user.username }}</span>
                                <span v-if="item.assign_to_user"> 承接人: {{ item.assign_to_user.username }}</span>
                            </li>
                            <li>创建时间: {{ item.created_at }}</li>
                            <li v-if="item.finish_at">结束时间: {{ item.finish_at }}</li>
                            <li>期望结束时间: {{ item.expect_finish_at }}</li>
                        </ul>
                    </router-link>
                </div>
            </div>
            <div class="row-pagination bg-white">
                <el-pagination
                    :total="tableData.total"
                    :page-size="tableData.per_page"
                    :current-page="tableData.current_page"
                    :pager-count="pagerCount"
                    @current-change="handleCurrentChange"
                    class="align-r"
                    background
                    hide-on-single-page
                    layout="total, prev, pager, next"
                >
                </el-pagination>
            </div>
        </div>
        <el-dialog
            :title="ticketDialogStatus==='create'?'发起工单':'编辑工单'"
            :visible.sync="popupAddticket"
            :width="$ismobile ? '320px' : '580px'"
            append-to-body
            :before-close="handleClose"
        >
            <el-form :model="ticketFormData" :rules="rules" ref="ticketForm">
                <el-form-item label="" prop="title">
                    <el-input v-model="ticketFormData.title" placeholder="工单标题"></el-input>
                </el-form-item>
                <el-form-item label="" prop="expect_finish_at">
                    <el-date-picker
                        v-model="ticketFormData.expect_finish_at"
                        :editable="false"
                        :picker-options="startDateOptions"
                        type="date"
                        placeholder="期望结束时间"
                        value-format="yyyy-MM-dd"
                        style="width:100%;"
                    >
                    </el-date-picker>
                </el-form-item>
            </el-form>
            <quill-editor
                v-model="ticketFormData.content"
                ref="myQuillEditor"
                :options="$ismobile ? editorOption2 : editorOption"
            >
            </quill-editor>
            <div class="upload-box">
                <el-upload
                    class="upload-demo"
                    action="no"
                    :http-request="uploadFile"
                    :on-remove="handleRemove"
                    :before-remove="beforeRemove"
                    multiple
                    :limit="5"
                    :on-exceed="handleExceed"
                    :file-list="fileList">
                    <el-button class="btn-upload" size="small">上传附件</el-button>
                </el-upload>
            </div>
            <el-button @click="saveTicket" class="addworkorder" type="success" size="small"><i class="el-icon-plus"></i>保存
            </el-button>
        </el-dialog>
        <el-dialog
            title="指派"
            :visible.sync="popupAssign"
            :width="$ismobile ? '320px' : '480px'"
            append-to-body
            :before-close="closePopupassign"
        >
            <div v-if="department.length > 0" class="assignbox clearfix">
                <el-select style="width: 100%" v-model="departmentfilterIndex" filterable placeholder="输入姓名或部门">
                    <el-option
                        v-for="(item,index) in departmentFilter"
                        :key="index"
                        :label="item.username"
                        :value="index">
                    </el-option>
                </el-select>
            </div>
            <el-button @click="confirmAssign" class="btn-confirmassign" type="success" size="small"><i
                class="el-icon-thumb"></i>确定指派
            </el-button>
        </el-dialog>
        <input style="display: none" id="uploadFile" @change="selectImg($event)" type="file"
               accept="image/jpeg,image/png,image/gif" name="filename"/>
    </div>
</template>

<script>
// @ is an alias to /src
import {createTicket, getDepartment, getStafflist, getTicketDetail, getTicketlist, postAssign, upload} from "@/api/api";
import breadCrumb from '@/components/breadcrumb'
import Tools from "@/components/tools.vue"
import mobileHeader from '@/components/mobile-header'
import topMenu from '@/components/top-menu'
import {store} from '@/store'

export default {
    components: {
        breadCrumb,
        Tools,
        mobileHeader,
        topMenu
    },
    data() {
        const that = this
        return {
            fileList: [],
            attachment: [],
            pagerCount: 7,
            staff: [], //员工列表
            department: [], //部门列表
            departmentFilter: [],
            departmentfilterIndex: "",
            depuserIndex: 0,
            departmentIndex: 0,
            content: '',
            popupAssign: false,
            editorOption: {
                placeholder: "请在这里输入",
                modules: {
                    toolbar: {
                        container: [
                            [{'header': [1, 2, 3, 4, 5, 6, false]}],     //几级标题
                            ['bold', 'italic', 'underline', 'strike'],    //加粗，斜体，下划线，删除线
                            [{'script': 'sub'}, {'script': 'super'}],   // 上下标
                            [{'color': []}, {'background': []}],     // 字体颜色，字体背景颜色
                            [{'align': []}],    //对齐方式
                            [{'indent': '-1'}, {'indent': '+1'}],     // 缩进
                            [{'list': 'ordered'}, {'list': 'bullet'}],     //列表
                            ['clean'],    //清除字体样式
                            ['link', 'image'],    //上传图片、上传视频
                        ],
                        handlers: {
                            'image': function (value) {
                                if (value) {
                                    document.querySelector('#uploadFile').click()
                                } else {
                                    this.quill.format('image', false);
                                }
                            }
                        }
                    }
                }
            },
            editorOption2: {
                placeholder: "请在这里输入",
                modules: {
                    toolbar: {
                        container: [
                            ['bold', 'italic', 'underline', 'strike'],    //加粗，斜体，下划线，删除线
                            [{'color': []}, {'background': []}],     // 字体颜色，字体背景颜色
                            [{'list': 'ordered'}, {'list': 'bullet'}],     //列表
                            ['image']    //上传图片、上传视频
                        ],
                        handlers: {
                            'image': function (value) {
                                if (value) {
                                    document.querySelector('#uploadFile').click()
                                } else {
                                    this.quill.format('image', false);
                                }
                            }
                        }
                    }
                }
            },
            endDateOptions: {
                disabledDate(time) {
                    if (that.form.start_date) {
                        const date = new Date(that.form.start_date)
                        return time.getTime() < date.getTime() - 24 * 60 * 60 * 1000
                    }
                }
            },
            startDateOptions: {
                disabledDate(time) {
                    const date = new Date()
                    return time.getTime() < date.getTime() - 24 * 60 * 60 * 1000
                }
            },
            tableData: [],
            form: {
                tab: 0,
                title: '',
                status: '0',
                created_by: '0',
                assign_to: 0,
                created_by_dept: 0,
                assign_to_dept: 0,
                is_overdue: '0',
                start_date: "",
                end_date: "",
                current_page: 1,
                per_page: 10
            },
            popupAddticket: false,
            ticketDialogStatus: "create",
            ChooseTicketid: 0,
            ticketFormData: {
                title: '',
                expect_finish_at: '',
                content: ''
            },
            filterPopup: false,
            showMobilemenu: false,
            rules: {
                title: [
                    {
                        required: true,
                        message: "This field is required.",
                        trigger: ["blur", "change"]
                    }
                ],
                expect_finish_at: [
                    {
                        required: true,
                        message: "This field is required.",
                        trigger: ["blur", "change"]
                    }
                ]
            },
        }
    },
    async created() {
        const [res1, res2] = await Promise.all([
            getStafflist(),
            getDepartment({"need_user": 1}),
        ])
        this.staff = res1.data.data.userList
        this.department = res2.data.data.deptList
        this.department.forEach(i => {
            i.dept_users.forEach(k => {
                this.departmentFilter.push({
                    dept_name: i.dept_name,
                    dept_id: i.dept_id,
                    username: i.dept_name + '：' + k.username,
                    uid: k.uid
                })
            })
        })
        this.initPagedata()
    },
    mounted() {
        this.fixInputfocus()
        if (window.innerWidth < 440) {
            this.pagerCount = 5
        }
    },
    watch: {
        $route: function () {
            this.showMobilemenu = false
            this.form.tab = Number(this.$route.query?.tab)
            this.initPagedata()
        }
    },
    computed: {
        titleData() {
            const titleData = {}
            switch (this.$route.query.tab) {
                case undefined:
                    titleData.name = "全部工单"
                    break
                case "0":
                    titleData.name = "全部工单"
                    break
                case "1":
                    titleData.name = "我发起的"
                    break
                case "2":
                    titleData.name = "我部门发起的"
                    break
                case "3":
                    titleData.name = "指派给我的"
                    break
                case "4":
                    titleData.name = "指派给我部门的"
                    break
            }
            return titleData
        }
    },
    methods: {
        async uploadFile(param) {
            const formData = new FormData()
            formData.append('file', param.file)
            const res = await upload(formData)
            this.attachment.push(
                {name: param.file.name, url: res.data.url}
            )
        },
        handleRemove(file, fileList) {
            this.attachment.forEach((i, index) => {
                if (i.name === file.name) {
                    this.attachment.splice(index, 1)
                }
            })
        },
        handleExceed(files, fileList) {
            this.$message.warning(`当前限制选择 5 个文件，本次选择了 ${files.length} 个文件，共选择了 ${files.length + fileList.length} 个文件`);
        },
        beforeRemove(file, fileList) {
            return this.$confirm(`确定移除 ${file.name}？`);
        },
        selectImg(event) {
            const file = event.target.files[0]
            let reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = (async (e) => {
                let base64Str = e.target.result.split(",")[1]
                const res = await upload({"img": base64Str})
                this.ticketFormData.content = this.ticketFormData.content + '<img src=' + res.data.url + ' />'
            })
        },
        fixInputfocus() {
            Array.from(document.getElementsByClassName('removeready')).forEach((item) => {
                item.children[0].children[0].removeAttribute('readonly')
                item.children[0].children[0].onblur = function () {
                    let _this = this
                    setTimeout(() => {
                        _this.removeAttribute('readonly')
                    }, 400)
                }
            })
            if (window.innerWidth < 767) {
                Array.from(document.getElementsByClassName('addready')).forEach((item) => {
                    item.children[1].setAttribute('readonly', 'readonly')
                    item.children[3].setAttribute('readonly', 'readonly')
                })
            }
        },
        handleClose() {
            this.$refs.ticketForm.resetFields()
            this.popupAddticket = false
        },
        addTicket() {
            this.fileList = []
            this.ticketDialogStatus = "create",
                this.ticketFormData = {
                    title: '',
                    expect_finish_at: '',
                    content: ''
                },
                this.popupAddticket = true
            if (this.ticketFormData.ticket_id !== undefined) {
                delete this.ticketFormData[ticket_id]
            }
        },
        async editTicket(id) {
            this.fileList = []
            const res = await getTicketDetail({'ticket_id': Number(id)})
            this.ticketDialogStatus = "edit"
            this.popupAddticket = true
            this.ticketFormData.ticket_id = id
            this.ticketFormData.title = res.data.data.ticketDetail.title
            this.ticketFormData.expect_finish_at = res.data.data.ticketDetail.expect_finish_at
            if (res.data.data.ticketDetail.content) {
                this.ticketFormData.content = res.data.data.ticketDetail.content
            }
            this.fileList = JSON.parse(res.data.data.ticketDetail.attachment)
            this.attachment = this.fileList
        },
        async initPagedata() {
            this.form.tab = Number(this.$route.query?.tab)
            this.form.current_page = Number(this.$route.query?.page)
            this.form.title = this.$route.query?.title
            this.form.status = this.$route.query?.status
            this.form.final_handler = Number(this.$route.query?.finalhandler) || 0
            this.form.created_by_dept = Number(this.$route.query?.createdbydept) || 0
            this.form.is_overdue = this.$route.query?.isoverdue
            this.form.created_by = this.$route.query?.createdby
            this.form.start_date = this.$route.query?.startdate
            this.form.end_date = this.$route.query?.enddate
            const res = await getTicketlist(this.form)
            this.tableData = res.data.data.ticketList
            this.tableData.data.forEach(i => {
                i.permission.new_assign?.forEach(k => {
                    if (store.state.userInfo.uInfo.uid === k) {
                        i.permission.showAssign = true
                    }
                })
                i.permission.re_assign?.forEach(k => {
                    if (store.state.userInfo.uInfo.uid === k) {
                        i.permission.showAssign = true
                    }
                })
                i.permission.edit?.forEach(k => {
                    if (store.state.userInfo.uInfo.uid === k) {
                        i.permission.showEdit = true
                    }
                })
            })
        },
        async search() {
            this.filterPopup = false
            this.$router.replace({
                query: {
                    ...this.$route.query,
                    title: this.form.title,
                    status: this.form.status,
                    finalhandler: this.form.final_handler,
                    createdbydept: this.form.created_by_dept,
                    isoverdue: this.form.is_overdue,
                    createdby: this.form.created_by,
                    startdate: this.form.start_date,
                    enddate: this.form.end_date,
                    page: 1
                }
            })
            this.initPagedata()
        },
        handleCurrentChange(val) {
            this.form.current_page = val
            this.$router.replace({
                query: {...this.$route.query, page: val}
            })
            this.initPagedata()
        },
        showPopupassign(id) {
            this.ChooseTicketid = id
            this.popupAssign = true
        },
        closePopupassign() {
            this.popupAssign = false
        },
        chooseDepartment(index) {
            this.departmentIndex = index
        },
        chooseDepuser(index) {
            this.depuserIndex = index
        },
        showFilterpopup() {
            this.filterPopup = true
            setTimeout(() => {
                this.fixInputfocus()
            }, 2000)
        },
        async confirmAssign() {
            const obj = {
                "ticket_id": this.ChooseTicketid,
                "assign_to_dept": this.departmentFilter[this.departmentfilterIndex].dept_id,
                "assign_to": this.departmentFilter[this.departmentfilterIndex].uid
            }
            await postAssign(obj)
            this.popupAssign = false
            this.$message({
                message: "指派成功",
                type: "success"
            })
            this.initPagedata()
            this.departmentfilterIndex = ""
        },
        async saveTicket() {
            this.$refs.ticketForm.validate(async valid => {
                if (valid) {
                    if (this.ticketFormData.content < 1) {
                        this.$message({
                            message: "内容不能为空",
                            type: "warning"
                        })
                        return false
                    }
                    this.ticketFormData.attachment = this.attachment
                    const res = await createTicket(this.ticketFormData)
                    if (res.data.code === 100) {
                        this.popupAddticket = false
                        if (this.ticketDialogStatus === "create") {
                            this.$message({
                                message: "创建成功",
                                type: "success"
                            })
                        } else {
                            this.$message({
                                message: "编辑成功",
                                type: "success"
                            })
                        }
                    }
                    this.attachment = []
                    this.initPagedata()
                    this.$refs.ticketForm.resetFields()
                }
            })
        }
    }
}
</script>
<style lang="scss">
.list {
    .el-table {
        min-height: 60vh;
    }
}

@media (max-width: 767px) {

}

.table-box-mobile {
    a {
        color: #333;
    }

    .card {
        font-size: 13px;
        line-height: 32px;
        border: 1px solid #e6e6e6;
        padding: 16px;
        background: #fff;
        margin-bottom: 16px;

        .title {
            font-size: 14px;
        }
    }

    .el-tag {
        margin-right: 8px;
    }
}

.assignbox {
    margin-bottom: 12px;
}
</style>
