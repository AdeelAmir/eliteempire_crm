<div class="modal fade" id="convertDispoLeadToLeadModal" tabindex="200" role="dialog" aria-labelledby="convertDispoLeadToLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            @if(Session::get('user_role') == 1)
            <form method="post" action="{{url('admin/dispo-lead/convert')}}" id="convertDispoLeadForm">
            @elseif(Session::get('user_role') == 2)
            <form method="post" action="{{url('general_manager/dispo-lead/convert')}}" id="convertDispoLeadForm">
            @elseif(Session::get('user_role') == 3)
            <form method="post" action="{{url('confirmationAgent/dispo-lead/convert')}}" id="convertDispoLeadForm">
            @endif
                @csrf
                <input type="hidden" name="convertDispoLeadId" id="convertDispoLeadId" value="" />
                <div class="modal-header">
                    <h5 class="modal-title" id="convertDispoLeadToLeadModalLabel">Convert to Lead</h5>
                </div>
                <div class="modal-body">
                    <p>Sure you want to convert this Dispo to Lead?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Convert</button>
                    <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
