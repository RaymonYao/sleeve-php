<template>
    <div class="list">
        <mobile-header v-if="$ismobile">
            <i @click="$router.back(-1)" v-if="$route.query.type !=='msg'" class="el-icon-arrow-left m-header-back"></i>
            <div class="title">工单详情</div>
            <i @click.stop="showMobilebtngroup" class="el-icon-more m-header-more"></i>
        </mobile-header>
        <div v-if="mobileBtngroup" class="mobile-ticket-btngroup">
            <ul>
                <li @click="replyTicket" v-if="detailData.status < 3" size="small" plain><i
                    class="el-icon-chat-dot-round"></i>回复工单
                </li>
                <li @click="showEditticket" v-if="this.showEdit" size="small" plain><i class="el-icon-edit"></i>编辑工单
                </li>
                <li @click="showPopupassign" v-if="this.showAssign" size="small" plain><i class="el-icon-thumb"></i>指派
                </li>
                <li @click="showOplog" size="small" plain><i class="el-icon-date"></i>日志</li>
                <li @click="showCompletepopup('complete')" v-if="this.showComplete" size="small" plain><i
                    class="el-icon-circle-check"></i>完成工单
                </li>
                <li @click="showCompletepopup('cancel')" v-if="this.showCancel" size="small" plain><i
                    class="el-icon-document-delete"></i>取消工单
                </li>
            </ul>
        </div>
        <top-menu :visible.sync="showMobilemenu"/>
        <div v-if="detailData.title" class="row bg-white">
            <div v-if="!$ismobile" class="row-inline clearfix">
                <bread-crumb/>
                <div v-if="!$ismobile" class="ticket-btngroup fr">
                    <el-button @click="showEditticket" v-if="this.showEdit" type="success" size="small" plain><i
                        class="el-icon-edit"></i>编辑工单
                    </el-button>
                    <el-button @click="showPopupassign" v-if="this.showAssign" type="success" size="small" plain><i
                        class="el-icon-thumb"></i>指派
                    </el-button>
                    <el-button @click="showOplog" type="success" size="small" plain><i class="el-icon-date"></i>日志
                    </el-button>
                    <el-button @click="showCompletepopup('complete')" v-if="this.showComplete" type="success"
                               size="small" plain><i class="el-icon-circle-check"></i>完成工单
                    </el-button>
                    <el-button @click="showCompletepopup('cancel')" v-if="this.showCancel" type="success" size="small"
                               plain><i class="el-icon-document-delete"></i>取消工单
                    </el-button>
                    <el-button @click="urgeTicket" v-if="showUrge" type="success" size="small" plain><i
                        class="el-icon-alarm-clock"></i>催单
                    </el-button>
                    <el-button v-if="detailData.status < 3" @click="replyTicket" class="newworkorder fr" type="success"
                               size="small"><i class="el-icon-chat-dot-round"></i>回复工单
                    </el-button>
                </div>
            </div>
            <div class="row-inline detail-bar">
                <ul class="clearfix">
                    <li>
                        <el-tag v-if="detailData.status === 1" size="small" type="">待处理</el-tag>
                        <el-tag v-if="detailData.status === 2" size="small" type="">处理中</el-tag>
                        <el-tag v-if="detailData.status === 3" size="small" type="success">已完成</el-tag>
                        <el-tag v-if="detailData.status === 4" size="small" type="danger"><i
                            class="el-icon-tickets"></i> <b>工单状态: 已取消</b></el-tag>
                    </li>
                    <li v-if="detailData.overdue_status !== 2">
                        <el-tag v-if="detailData.overdue_status === 2" size="small" type="success">未逾期</el-tag>
                        <el-tag v-if="detailData.overdue_status === 3" size="small" type="warning">将逾期</el-tag>
                        <el-tag v-if="detailData.overdue_status === 1" size="small" type="danger">已逾期</el-tag>
                    </li>
                    <li v-if="detailData.created_user">
                        <el-tag size="small" type="info"><i class="el-icon-user-solid"></i> 发起人:
                            {{ detailData.created_user.username }}
                        </el-tag>
                    </li>
                    <li v-if="detailData.assign_to_user">
                        <el-tag size="small" type="info"><i class="el-icon-s-custom"></i> 承接人:
                            {{ detailData.assign_to_user.username }}
                        </el-tag>
                    </li>
                    <li>
                        <el-tag size="small" type="info"><i class="el-icon-date"></i> 创建时间: {{
                                detailData.created_at
                            }}
                        </el-tag>
                    </li>
                    <li class="expect-time fr">
                        期望结束时间: {{ detailData.expect_finish_at }}
                    </li>
                </ul>
            </div>
            <div v-if="downloadFile.length > 0" class="row-inline detail-download">
                <span @click="download(item.url,item.name)" v-for="item in downloadFile"><i
                    class="el-icon-download"></i> {{ item.name }}</span>
            </div>
        </div>
        <div v-if="detailData.created_user" class="row bg-white ticket-floor autoheight">
            <div class="ticket-content">
                <div class="s-title">{{ detailData.title }} <span>({{ detailData.ticket_id }})</span></div>
                <div class="s-header">
                    <div v-if="detailData.created_user.avatar !==''" class="user-avatar">
                        <img :src="detailData.created_user.avatar">
                    </div>
                    <div class="default-avatar" v-else>
                        <i class="el-icon-s-custom"></i>
                    </div>
                    {{ detailData.created_dept ? detailData.created_dept.dept_name : '[未知部门]' }}
                    {{ detailData.created_user.username }}
                    <div class="fr ticket-floor-date"><span v-if="!$ismobile">创建时间:</span> {{
                            detailData.created_at
                        }}
                    </div>
                </div>
                <div v-html="detailData.content" class="s-body"></div>
            </div>
        </div>
        <div v-for="item in replyData" class="row bg-white ticket-floor autoheight">
            <div class="reply-content">
                <el-tag
                    type="info"
                    class="othersreplyinfo"
                    v-if="item.replayInfo"
                >
                    {{ item.replayInfo.dept_name }}
                    {{ item.replayInfo.username }}
                    {{ item.replayInfo.content }}
                </el-tag>
                <div class="s-header">
                    <div v-if="item.created_user && item.created_user.avatar !==''" class="user-avatar">
                        <img :src="item.created_user.avatar">
                    </div>
                    <div class="default-avatar" v-else>
                        <i class="el-icon-s-custom"></i>
                    </div>
                    {{ item.created_user ? item.created_user.dept_name : '[离职人员]' }}
                    {{ item.created_user ? item.created_user.username : '(未知)' }}
                    <div class="fr ticket-floor-date"><span v-if="!$ismobile">回复时间:</span> {{ item.created_at }}
                    </div>
                </div>
                <div v-html="item.content" class="s-body">
                </div>
                <div v-if="item.attachment !== '[]'" class="s-download">
                    <span @click="download(file.url,file.name)" v-for="file in JSON.parse(item.attachment)"><i
                        class="el-icon-download"></i> {{ file.name }}</span>
                </div>
                <span
                    @click="othersReply((item.created_user ? item.created_user.username : '未知'),item.content,item.post_id)"
                    class="btn-reply">回复</span>
            </div>
        </div>
        <div v-if="detailData.status < 3" class="row bg-white ticket-floor">
            <div class="s-header">工单回复</div>
            <el-tag
                closable
                class="othersreplyinfo"
                @close="othersReplyinfo.info = ''"
                v-if="othersReplyinfo.info.length > 0"
            >
                {{ othersReplyinfo.info }}
            </el-tag>
            <div class="s-body">
                <quill-editor
                    v-model="replyContentdata"
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
                <el-button @click="saveReply" class="savereply" type="success" size="small"><i
                    class="el-icon-chat-line-square"></i>提交回复
                </el-button>
            </div>
        </div>
        <el-dialog
            title="操作日志"
            :visible.sync="popupOplog"
            :width="$ismobile ? '320px' : '580px'"
            append-to-body
            :before-close="CloseOplog"
        >
            <ul class="oplog-list">
                <li class="clearfix" v-for="item in oplogData"><p class="fl">
                    {{ item.op_user ? item.op_user.username : '[离职人员]' }} {{ item.description }} </p><span
                    class="fr">{{ item.created_at }}</span></li>
            </ul>
        </el-dialog>
        <el-dialog
            title="指派"
            :visible.sync="popupAssign"
            :width="$ismobile ? '320px' : '480px'"
            append-to-body
            :before-close="closePopupassign"
        >
            <el-select style="width: 100%" v-model="departmentfilterIndex" filterable placeholder="输入姓名或部门">
                <el-option
                    v-for="(item,index) in departmentFilter"
                    :key="index"
                    :label="item.username"
                    :value="index">
                </el-option>
            </el-select>
            <el-button @click="confirmAssign" class="btn-confirmassign" type="success" size="small"><i
                class="el-icon-thumb"></i>确定指派
            </el-button>
        </el-dialog>
        <el-dialog
            title="编辑工单"
            :visible.sync="popupEditticket"
            :width="$ismobile ? '320px' : '580px'"
            append-to-body
            :before-close="closeEditticket"
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
            <el-button @click="saveTicket" class="addworkorder" type="success" size="small"><i class="el-icon-plus"></i>保存
            </el-button>
        </el-dialog>
        <el-dialog
            class="popup-cbox"
            :title="completePopupstatus==='complete'?'完成工单':'取消工单'"
            :visible.sync="popupComplete"
            :width="$ismobile ? '240px' : '320px'"
            append-to-body
            :before-close="closePopupstatus"
        >
            <span v-if="completePopupstatus==='complete'"><i
                class="el-icon-warning-outline"></i>确认要完成工单吗？</span>
            <span v-if="completePopupstatus==='cancel'"><i class="el-icon-warning-outline"></i>确认要取消工单吗？</span>
            <span slot="footer" class="dialog-footer">
        <el-button @click="closePopupstatus" size="small">取 消</el-button>
        <el-button type="primary" @click="savePopupstatus" size="small">确 定</el-button>
      </span>
        </el-dialog>
        <input style="display:none;" id="uploadFile" @change="selectImg($event)" type="file"
               accept="image/jpeg,image/png,image/gif" name="filename"/>
    </div>
</template>

<script>
// @ is an alias to /src
import {
    cancelTicket,
    completeTicket,
    createTicket,
    getDepartment,
    getOplog,
    getReply,
    getTicketDetail,
    postAssign,
    postReply,
    upload,
    urgeTicket
} from "@/api/api";
import breadCrumb from '@/components/breadcrumb'
import mobileHeader from '@/components/mobile-header'
import topMenu from '@/components/top-menu'
import {store} from '@/store'

export default {
    components: {
        breadCrumb,
        mobileHeader,
        topMenu
    },
    data() {
        return {
            fileList: [],
            attachment: [],
            depuserIndex: 0,
            departmentIndex: 0,
            department: [], //部门列表
            departmentFilter: [],
            departmentfilterIndex: "",
            showMobilemenu: false,
            showCancel: false,
            showAssign: false,
            showEdit: false,
            showComplete: false,
            showUrge: false,
            popupOplog: false,
            oplogData: [],
            popupAssign: false,
            popupEditticket: false,
            completePopupstatus: "complete",
            popupComplete: false,
            mobileBtngroup: false,
            downloadFile: [],
            othersReplyinfo: {
                parentId: 0,
                info: ""
            },
            ticketFormData: {
                ticket_id: 0,
                title: '',
                expect_finish_at: '',
                content: ''
            },
            detailData: {},
            replyData: {},
            replyContentdata: "",
            startDateOptions: {
                disabledDate(time) {
                    const date = new Date()
                    return time.getTime() < date.getTime() - 24 * 60 * 60 * 1000
                }
            },
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
            }
        }
    },
    async created() {
        const res = await getDepartment({"need_user": 1})
        this.department = res.data.data.deptList
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
        document.querySelector("#app").addEventListener('click', () => {
            this.mobileBtngroup = false
        })
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
                if (this.popupEditticket) {
                    this.ticketFormData.content = this.ticketFormData.content + '<img src=' + res.data.url + ' />'
                } else {
                    this.replyContentdata = this.replyContentdata + '<img src=' + res.data.url + ' />'
                }
            })
        },
        async replyTicket() {
            this.mobileBtngroup = false
            this.othersReplyinfo.info = ""
            window.scrollTo(0, document.body.scrollHeight)
        },
        async initPagedata() {
            const res1 = await getTicketDetail({'ticket_id': Number(this.$route.query.id)})
            this.detailData = res1.data.data.ticketDetail
            this.downloadFile = JSON.parse(res1.data.data.ticketDetail.attachment)
            const res2 = await getReply({'ticket_id': Number(this.$route.query.id)})
            this.replyData = res2.data.data.postList
            this.showCancel = false
            this.showComplete = false
            this.showAssign = false
            this.showEdit = false
            this.filterBtn()
            this.filterReplyuser()
        },
        filterReplyuser() {
            this.replyData.forEach(i => {
                if (i.parent_id > 0) {
                    this.replyData.forEach(k => {
                        if (i.parent_id === k.post_id) {
                            i.replayInfo = k.created_user ? k.created_user : {}
                            i.replayInfo.content = k.content
                            i.replayInfo.content = i.replayInfo.content.replace(/<[^>]+>/g, "")
                            if (i.replayInfo.content.length > 20) {
                                i.replayInfo.content = i.replayInfo.content.substring(0, 20) + "..."
                            }
                        }
                    })
                }
            })
        },
        filterBtn() {
            this.detailData.permission.cancel?.forEach(i => {
                if (store.state.userInfo.uInfo.uid === i) {
                    this.showCancel = true
                }
            })
            this.detailData.permission.complete?.forEach(i => {
                if (store.state.userInfo.uInfo.uid === i) {
                    this.showComplete = true
                }
            })
            this.detailData.permission.complete_assign_tk?.forEach(i => {
                if (store.state.userInfo.uInfo.uid === i) {
                    this.showComplete = true
                }
            })
            this.detailData.permission.re_assign?.forEach(i => {
                if (store.state.userInfo.uInfo.uid === i) {
                    this.showAssign = true
                }
            })
            this.detailData.permission.new_assign?.forEach(i => {
                if (store.state.userInfo.uInfo.uid === i) {
                    this.showAssign = true
                }
            })
            this.detailData.permission.edit?.forEach(i => {
                if (store.state.userInfo.uInfo.uid === i) {
                    this.showEdit = true
                }
            })
            this.detailData.permission.urge?.forEach(i => {
                if (store.state.userInfo.uInfo.uid === i) {
                    this.showUrge = true
                }
            })
        },
        othersReply(name, content, id) {
            let con = content.replace(/<[^>]+>/g, "")
            if (con.length > 20) {
                con = con.substring(0, 20) + "..."
            }
            this.othersReplyinfo.info = name + con
            this.othersReplyinfo.parentId = id
            window.scrollTo(0, document.body.scrollHeight)
        },
        async saveReply() {
            if (this.replyContentdata.length < 1) {
                this.$message({
                    message: "内容不能为空",
                    type: "warning"
                })
                return false
            }
            const replyData = {
                ticket_id: this.$route.query.id,
                content: this.replyContentdata,
                attachment: this.attachment
            }
            if (this.othersReplyinfo.info.length > 0) {
                replyData.parent_id = this.othersReplyinfo.parentId
            }
            const res = await postReply(replyData)
            if (res.data.code === 100) {
                this.replyContentdata = ""
                this.othersReplyinfo.info = ""
                this.$message({
                    message: "回复成功",
                    type: "success"
                })
                this.initPagedata()
            }
        },
        async showOplog() {
            this.mobileBtngroup = false
            const res = await getOplog({'ticket_id': Number(this.$route.query.id)})
            this.oplogData = res.data.data.ticketList
            this.popupOplog = true
        },
        CloseOplog() {
            this.popupOplog = false
        },
        showPopupassign() {
            this.mobileBtngroup = false
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
        showEditticket() {
            this.ticketFormData.title = this.detailData.title
            this.ticketFormData.expect_finish_at = this.detailData.expect_finish_at
            this.ticketFormData.content = this.detailData.content
            this.mobileBtngroup = false
            this.popupEditticket = true
        },
        closeEditticket() {
            this.popupEditticket = false
        },
        showCompletepopup(state) {
            this.mobileBtngroup = false
            this.popupComplete = true
            this.completePopupstatus = state
        },
        closePopupstatus() {
            this.popupComplete = false
        },
        showMobilebtngroup() {
            this.mobileBtngroup = !this.mobileBtngroup
        },
        async savePopupstatus() {
            if (this.completePopupstatus === 'complete') {
                await completeTicket({"ticket_id": this.detailData.ticket_id})
                this.$message({
                    message: "工单完成成功",
                    type: "success"
                })
            } else {
                await cancelTicket({"ticket_id": this.detailData.ticket_id})
                this.$message({
                    message: "工单取消成功",
                    type: "success"
                })
            }
            this.popupComplete = false
            this.initPagedata()
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
                    this.ticketFormData.ticket_id = this.detailData.ticket_id
                    await createTicket(this.ticketFormData)
                    this.popupEditticket = false
                    this.$message({
                        message: "编辑成功",
                        type: "success"
                    })
                    this.initPagedata()
                }
            })
        },
        async confirmAssign() {
            const obj = {
                "ticket_id": this.detailData.ticket_id,
                "assign_to_dept": this.departmentFilter[this.departmentfilterIndex].dept_id,
                "assign_to": this.departmentFilter[this.departmentfilterIndex].uid
            }
            const res = await postAssign(obj)
            if (res.data.code === 100) {
                this.$message({
                    message: "指派成功",
                    type: "success"
                })
            }
            this.initPagedata()
            this.popupAssign = false
        },
        async urgeTicket() {
            const res = await urgeTicket({"ticket_id": this.detailData.ticket_id})
            if (res.data.code === 100) {
                this.$message({
                    message: "催单成功",
                    type: "success"
                })
            }
        },
        download(url, filename) {
            let _this = this
            this.getBlob(url).then(function (blob) {
                _this.saveas(blob, filename);
            })
        },
        getBlob(url) {
            return new Promise(function (resolve) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url, true);
                xhr.responseType = 'blob';
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        resolve(xhr.response);
                    }
                };
                xhr.send();
            })
        },
        saveas(blob, filename) {
            if (window.navigator.msSaveOrOpenBlob) {
                navigator.msSaveBlob(blob, filename);
            } else {
                var link = document.createElement('a');
                var body = document.querySelector('body');

                link.href = window.URL.createObjectURL(blob);
                link.download = filename;

                // fix Firefox
                link.style.display = 'none';
                body.appendChild(link);

                link.click();
                body.removeChild(link);

                window.URL.revokeObjectURL(link.href);
            }
        }
    }
}
</script>
<style lang="scss">
.detail-bar {
    padding-top: 16px;

    li {
        float: left;
        margin-right: 16px;

        .el-tag {
            padding: 4px 10px;
            line-height: 16px;
            height: auto;
            border: 1px dashed;
            font-size: 13px;

            i {
                font-size: 16px;
                vertical-align: bottom;
                font-weight: 600;
            }

            .el-icon-s-custom, .el-icon-user-solid {
                font-weight: 300;
            }
        }
    }
}

.expect-time {
    font-size: 14px;
    float: right !important;
    line-height: 24px;
    margin: 0 !important;
    color: #666;
}

.detail-download {
    font-size: 14px;
    padding-top: 16px;

    span {
        margin-right: 24px;
        color: #666;
        cursor: pointer;
    }
}

.s-download {
    font-size: 13px;
    padding: 8px 0;
    border-top: 1px dashed #e6e6e6;

    span {
        color: #666;
        cursor: pointer;
    }
}

.ticket-floor {
    padding: 0 16px !important;

    .reply-content {
        position: relative;
    }

    .s-title {
        padding: 10px 16px;
        margin-right: -16px;
        margin-left: -16px;
        font-weight: 600;
        border-bottom: 1px solid #e6e6e6;
        font-size: 14px;
        background: #888;
        color: #fff;
    }

    .s-header {
        padding: 10px 0;
        font-weight: 600;
        border-bottom: 1px solid #e6e6e6;
        font-size: 14px;

        span {
            font-weight: normal;
        }
    }

    .s-body {
        padding: 10px 0;
        font-size: 14px;

        img {
            max-width: 100%;
            margin-bottom: 24px;
        }
    }

    .savereply {
        margin-top: 16px;
    }

    .btn-reply {
        color: #333;
        font-size: 13px;
        position: absolute;
        bottom: 10px;
        right: 0px;
        color: #409eff;
        cursor: pointer;
    }
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 36px;
    overflow: hidden;
    display: inline-block;
    vertical-align: middle;

    img {
        max-width: 100%;
        max-height: 100%;
    }
}

.default-avatar {
    width: 36px;
    height: 36px;
    border-radius: 36px;
    background: #efefef;
    overflow: hidden;
    text-align: center;
    display: inline-block;
    vertical-align: middle;

    i {
        font-size: 24px;
        color: #999;
        margin: 4px auto;
    }
}

.reply-content {
    .othersreplyinfo {
        margin-bottom: 8px;
    }
}

.othersreplyinfo {
    display: block !important;
    font-size: 13px !important;
    margin-top: 16px;
    position: relative;

    i {
        position: absolute !important;
        top: 8px !important;
        right: 5px !important;
    }
}

.oplog-list {
    display: block;
    min-height: 240px;
    max-height: 480px;
    overflow: auto;

    li {
        padding: 8px 0;
        border-bottom: 1px dashed #e6e6e6;
    }
}

.assignbox {
    .assignbox-dep {
        width: 210px;
        height: 200px;
        border: 1px solid #e6e6e6;
        margin-right: 16px;
        overflow: auto;
        border-radius: 6px;
    }

    .assignbox-user {
        width: 210px;
        height: 200px;
        border: 1px solid #e6e6e6;
        overflow: auto;
        border-radius: 6px;
    }

    li {
        padding: 6px 0 6px 24px;
        position: relative;
        border-bottom: 1px dashed #f1f1f1;

        i {
            position: absolute;
            left: 6px;
            top: 10px;
        }
    }

    .active {
        background: #ecf5ff;
        color: #1989fa;
    }
}

.btn-confirmassign {
    margin-top: 16px !important;
}

.popup-cbox {
    .el-dialog__header {
        display: none;
    }

    .el-dialog__body {
        text-align: center;
        padding-top: 24px;

        i {
            font-size: 20px;
            margin-right: 4px;
            vertical-align: middle;
        }
    }

    .el-dialog__footer {
        text-align: center;
    }
}

.mobile-ticket-btngroup {
    position: fixed;
    right: 8px;
    width: 160px;
    background: #fff;
    z-index: 10;
    padding: 0 16px;
    font-size: 13px;
    border-radius: 3px;
    top: 40px;
    box-shadow: 1px 1px 5px #e6e6e6;

    li {
        padding: 8px 0;
        border-bottom: 1px dashed #e6e6e6;

        i {
            margin-right: 8px;
        }

        &:last-child {
            border: 0;
        }
    }
}

.ticket-title span {
    font-size: 14px;
}

.ticket-floor-date {
    font-weight: normal;
    margin-top: 8px;
}

@media (max-width: 767px) {
    .detail-bar, .row-inline {
        margin: 0;
        padding-top: 0;
    }
    .detail-download {
        font-size: 14px;
        padding-top: 4px;
        padding-bottom: 4px !important;

        span {
            margin-right: 0;
            color: #666;
            display: block;
        }
    }
    .mobile-resize {
        padding-top: 8px;
        padding-bottom: 8px !important;
    }
    .detail-bar {
        overflow: auto;

        ul {
            white-space: nowrap;

            li {
                display: inline-block;
                float: none;
            }
        }
    }
    .oplog-list p {
        max-width: calc(100% - 140px);
    }
    .assignbox {
        .assignbox-dep {
            width: 130px;
        }

        .assignbox-user {
            width: 130px;
        }
    }
    .ticket-floor {
        .s-header {
            font-size: 13px;
        }
    }
    .ticket-floor-date {
        font-size: 13px;
        font-weight: normal;
        margin-top: 8px;
    }
    .expect-time {
        float: none !important;
    }
}
</style>
